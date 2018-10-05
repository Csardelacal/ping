(function () {
	
	"use strict";
	
	class Job {
		/**
		 * @param {Queue} queue
		 */
		constructor(queue) {
			this._completed = false;
			this._queue = queue;
		}
		complete(){
			this._completed = true;
			this._queue.notify();
		}
		isComplete(){
			return this._completed;
		}
	}
	
	/**
	 * The queue will be a set of jobs together with listeners for both the oncomplete
	 * and onprogress statuses.
	 * 
	 * We do use a queue element instead of promises since a queue can change it's
	 * state back and forth depending on whether new elements were pushed to it.
	 */
	class Queue {
		constructor(){
			/**
			 * The jobs array contains the jobs that we pushed into the queue. This way
			 * the queue can keep track of it's progress and notify it's target once
			 * it's done.
			 *
			 * @type Array
			 */
			this._jobs = [];
		}
		
		/**
		 * This function is used by the jobs to notify the queue that their status
		 * has changed.
		 * 
		 * @returns {undefined}
		 */
		notify(){
			let completed = 0;
			const total = this._jobs.length;
			
			for (let i = 0; i < total; i++) {
				if (this._jobs[i].isComplete()) { completed++; }
			}
			
			completed === total ? this.onComplete() : this.onProgress({progress : completed / total});
		};
		
		/**
		 * Adds a new job to the queue and returns it so the user can make use of 
		 * it.
		 * 
		 * @returns {Job}
		 */
		job(){
			var j = new Job(this);
			
			this._jobs.push(j);
			this.notify();
			
			return j;
		};

		onProgress(e){}
		onComplete(e){}
	}
	
	window.Queue = Queue;
	
}());
