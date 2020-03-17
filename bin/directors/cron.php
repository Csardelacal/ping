<?php

use auth\SSOCache;
use cron\FlipFlop;
use cron\TimerFlipFlop;
use media\Compressor;
use ping\embed\NotYetAvailableException;
use ping\embed\PssmsShortener;
use spitfire\core\Environment;
use spitfire\mvc\Director;

/* 
 * The MIT License
 *
 * Copyright 2018 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class CronDirector extends Director
{
	
	
	public function email() {
		
		console()->success('Initiating cron...')->ln();
		$started   = time();
		$old       = false;
		
		
		$file = spitfire()->getCWD() . '/bin/usr/.cron.lock';
		$fh = fopen($file, file_exists($file)? 'r' : 'w+');
		
		if (!flock($fh, LOCK_EX)) { 
			console()->error('Could not acquire lock')->ln();
			return 1; 
		}
		
		console()->success('Acquired lock!')->ln();
		
		while(null !== $old  = db()->table('email\digestqueue')->get('created', time() - 86400, '<')->fetch()) {
			
			$user = $old->notification->target;
			
			$emailsender = new EmailSender($this->sso);
			$emailsender->sendDigest($this->sso->getUser($user->authId));

			#Delete the digest queue
			$q  = db()->table('email\digestqueue')->get('user', $user);

			$res = $q->fetchAll();
			foreach ($res as $record) { $record->delete(); }
			
			if (time() > $started + 1200) {
				break;
			}
		}
		
		console()->success('Cron ended, was running for ' . (time() - $started) . ' seconds')->ln();
		
		flock($fh, LOCK_UN);
		
		return 0;
		
	}
	
	public function media() {
		#Create a user
		$this->sso   = new SSOCache(Environment::get('SSO'));
		
		console()->success('Initiating cron...')->ln();
		$started   = time();
		$ping      = false;
		
		
		$file = spitfire()->getCWD() . '/bin/usr/.media.cron.lock';
		$fh = fopen($file, file_exists($file)? 'r' : 'w+');
		
		/**
		 * Prepare a semaphore based flip-flop.
		 */
		try {
			$sem = new FlipFlop($file);
		} 
		catch (Exception $ex) {
			$sem = new TimerFlipFlop($file);
		}
		
		$next = function () {
			return db()->table('ping')->getAll()
				->group()->where('processed', false)->where('processed', null)->endGroup()
				->group()->where('locked', false)->where('locked', null)->endGroup()
				->first();
		};
				
		while(
			(flock($fh, LOCK_EX) && null !== ($ping = $next())) ||
			$sem->wait()
		) {
			/*
			 * Check if the cron has been running long enough that we should consider
			 * stopping it.
			 */
			if (time() - $started > 1440) {
				break;
			}
			
			if (!$ping) {
				continue;
			}
			
			console()->success('Acquired lock!')->ln();
			
			$ping->locked = true;
			$ping->store();
			
			flock($fh, LOCK_UN);

			
			$attached = $ping->attached->toArray();
			
			if (empty($attached) && $ping->media) {
				$file = storage()->dir(Environment::get('uploads.directory'))->make(uniqid() . str_replace(['?', '%'], '', pathinfo($ping->media, PATHINFO_BASENAME)));
				
				try {
					$file->write(storage()->get($ping->media)->read());
				} 
				catch (Exception $ex) {
					$body = file_get_contents($ping->media);
					if (!strstr($http_response_header[0], '200')) { 
						$ping->processed = true;
						$ping->store();
						continue;
					}
					$file->write($body);
				}
				
				$media = db()->table('media\media')->newRecord();
				$media->type = 'image';
				$media->file = $file->uri();
				$media->ping = $ping;
				$media->store();
				
				$attached[] = $media;
				
				console()->success('Created fallback media')->ln();
			}
			
			foreach ($attached as $media) {
				$micro = microtime(true);
				
				$compressor = new Compressor($media);
				$compressor->process();
				
				console()->success('Processed media, took ' . (microtime(true) - $micro) . ' seconds')->ln();
			}
			
			console()->success('Processed ping')->ln();
			
			$ping->processed = true;
			$ping->locked = false;
			$ping->store();
		}
		
		console()->success('Cron ended, was running for ' . (time() - $started) . ' seconds')->ln();		
		return 0;
	}
	
	public function url() {
		
		#Set up the link shortener
		$shortener = new PssmsShortener(Environment::get('shortener.url'));
		
		#First, loop over elements that have a URL assigned, but it's not yet in the embed table
		$pings = db()->table('ping')->get('url', null, '!=')->range(0, 500);
		
		foreach ($pings as $ping) {
			$embed = db()->table('embed')->newRecord();
			$embed->ping = $ping;
			$embed->url = $ping->url;
			$embed->store();
			
			$ping->url = null;
			$ping->store();
			
			console()->success('Migrated ping to embed')->ln();
			sleep(1);
		}
		
		#Now, loop over the embeds with no short URL and shorten it
		if ($shortener) {
			$longs = db()->table('embed')->get('short', null)->range(0, 500);
			
			foreach ($longs as $long) {
				$long->short = $shortener->shorten($long->url);
				$long->store();

				console()->success('Shortened ' . $long->url . ' to ' . $long->short)->ln();
				sleep(1);
			}
		}
		
		#Finally, fetch the fetch data for all of the shortened URLs
		if ($shortener) {
			$unfetched = db()->table('embed')->get('title', null)->range(0, 300);
			
			foreach ($unfetched as $pending) {
				try {
					$meta = $shortener->read($pending->short);
					$pending->title = substr($meta->getTitle()?: 'Untitled', 0, 64);
					$pending->description = substr($meta->getDescription()?: 'No description available', 0, 255);
					$pending->image = substr($meta->getImage()?: null, 0, 255);
					$pending->store();
				} 
				catch (NotYetAvailableException$ex) {
					console()->error('Tried to fetch metadata about URL that has not yet become available')->ln();
				}
			}
		}
	}
	
}
