(function () {
	
	"use strict";
	
	function Job(queue) {
		var completed = false;
		
		this.complete = function () {
			completed = true;
			queue.notify();
		};
		
		this.isComplete = function () {
			return !!completed;
		};
		
	}
	
	/**
	 * The queue will be a set of jobs together with listeners for both the oncomplete
	 * and onprogress statuses.
	 * 
	 * We do use a queue element instead of promises since a queue can change it's
	 * state back and forth depending on whether new elements were pushed to it.
	 * 
	 * @returns {undefined}
	 */
	function Queue() {
		
		/**
		 * The jobs array contains the jobs that we pushed into the queue. This way
		 * the queue can keep track of it's progress and notify it's target once
		 * it's done.
		 * 
		 * @type Array
		 */
		this.jobs = [];
		
	}
	
	Queue.prototype = {
		onProgress: function (e) {},
		onComplete: function (e) {},
		
		/**
		 * This function is used by the jobs to notify the queue that their status
		 * has changed.
		 * 
		 * @returns {undefined}
		 */
		notify: function () {
			var completed = 0;
			var total     = this.jobs.length;
			
			for (var i = 0; i < total; i++) {
				if (this.jobs[i].isComplete()) { completed++; }
			}
			
			completed === total? this.onComplete() : this.onProgress({progress : completed / total});
		},
		
		/**
		 * Adds a new job to the queue and returns it so the user can make use of 
		 * it.
		 * 
		 * @returns {queue_L1.Job}
		 */
		job: function () {
			var j = new Job(this);
			
			this.jobs.push(j);
			this.notify();
			
			return j;
		}
	};
	
	window.Queue = Queue;
	depend && depend(function () { return Queue; });
	
}());