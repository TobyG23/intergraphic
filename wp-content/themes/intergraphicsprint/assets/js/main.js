/**
 * Main JavaScript for Inter Graphics Print Theme
 *
 * @package InterGraphicsPrint
 * @version 2.0.0
 */

(function($) {
	'use strict';

	/**
	 * Theme namespace
	 */
	var IGP = {
		/**
		 * Initialize theme
		 */
		init: function() {
			this.setupDOM();
			this.setupEvents();
			this.setupComponents();
		},

		/**
		 * Setup DOM elements
		 */
		setupDOM: function() {
			this.$window = $(window);
			this.$document = $(document);
			this.$body = $('body');
			this.$header = $('#header-outer');
		},

		/**
		 * Setup event listeners
		 */
		setupEvents: function() {
			var self = this;

			// Scroll events
			this.$window.on('scroll', function() {
				self.handleScroll();
			});

			// Resize events
			this.$window.on('resize', function() {
				self.handleResize();
			});

			// Alert close buttons
			this.$document.on('click', '.alert-close', function() {
				$(this).closest('.alert').fadeOut(function() {
					$(this).remove();
				});
			});

			// Smooth scroll for anchor links
			this.$document.on('click', 'a[href^="#"]', function(e) {
				var href = $(this).attr('href');
				if ($(href).length) {
					e.preventDefault();
					$('html, body').animate({
						scrollTop: $(href).offset().top - 80
					}, 800, 'easeInOutQuad');
				}
			});
		},

		/**
		 * Setup components
		 */
		setupComponents: function() {
			this.initializeButtons();
			this.initializeForms();
			this.initializeTooltips();
			this.initializeModals();
		},

		/**
		 * Initialize buttons
		 */
		initializeButtons: function() {
			var self = this;

			$('.btn, button, input[type="submit"], input[type="button"]').on('click', function(e) {
				// Add ripple effect
				self.createRipple(this, e);
			});
		},

		/**
		 * Initialize forms
		 */
		initializeForms: function() {
			// Add active class to form labels when input is focused
			$('input, textarea, select').on('focus', function() {
				$(this).closest('.form-group').addClass('active');
			});

			$('input, textarea, select').on('blur', function() {
				if (!$(this).val()) {
					$(this).closest('.form-group').removeClass('active');
				}
			});

			// Form validation
			$('form').on('submit', function(e) {
				if (!this.checkValidity()) {
					e.preventDefault();
					e.stopPropagation();
				}
				$(this).addClass('was-validated');
			});
		},

		/**
		 * Initialize tooltips
		 */
		initializeTooltips: function() {
			$('[data-tooltip]').on('mouseenter', function() {
				var text = $(this).attr('data-tooltip');
				var position = $(this).attr('data-tooltip-position') || 'top';

				var $tooltip = $('<div class="tooltip" />')
					.text(text)
					.addClass('tooltip-' + position)
					.appendTo('body');

				var offset = $(this).offset();
				var height = $(this).outerHeight();
				var width = $(this).outerWidth();

				var top, left;
				switch(position) {
					case 'bottom':
						top = offset.top + height + 10;
						left = offset.left + (width / 2) - ($tooltip.outerWidth() / 2);
						break;
					case 'left':
						top = offset.top + (height / 2) - ($tooltip.outerHeight() / 2);
						left = offset.left - $tooltip.outerWidth() - 10;
						break;
					case 'right':
						top = offset.top + (height / 2) - ($tooltip.outerHeight() / 2);
						left = offset.left + width + 10;
						break;
					default: // top
						top = offset.top - $tooltip.outerHeight() - 10;
						left = offset.left + (width / 2) - ($tooltip.outerWidth() / 2);
				}

				$tooltip.css({
					position: 'fixed',
					top: top,
					left: left,
					zIndex: 1000
				}).fadeIn();

				$(this).on('mouseleave', function() {
					$tooltip.fadeOut(function() {
						$(this).remove();
					});
				});
			});
		},

		/**
		 * Initialize modals
		 */
		initializeModals: function() {
			var self = this;

			// Open modal
			$('[data-modal]').on('click', function() {
				var modalId = $(this).attr('data-modal');
				self.openModal(modalId);
			});

			// Close modal
			$('.modal-close, .modal-backdrop').on('click', function() {
				self.closeModal();
			});

			// Prevent closing when clicking inside modal
			$('.modal-content').on('click', function(e) {
				e.stopPropagation();
			});
		},

		/**
		 * Create ripple effect on click
		 */
		createRipple: function(element, event) {
			var $ripple = $('<span class="ripple"></span>');
			var rect = element.getBoundingClientRect();

			var size = Math.max(rect.width, rect.height);
			var x = event.clientX - rect.left - size / 2;
			var y = event.clientY - rect.top - size / 2;

			$ripple.css({
				width: size,
				height: size,
				left: x,
				top: y
			}).appendTo(element);

			setTimeout(function() {
				$ripple.remove();
			}, 600);
		},

		/**
		 * Handle scroll events
		 */
		handleScroll: function() {
			var scrollTop = this.$window.scrollTop();

			// Add sticky class to header
			if (scrollTop > 100) {
				this.$header.addClass('sticky-active');
			} else {
				this.$header.removeClass('sticky-active');
			}
		},

		/**
		 * Handle resize events
		 */
		handleResize: function() {
			// Handle responsive behavior
		},

		/**
		 * Open modal
		 */
		openModal: function(modalId) {
			var $modal = $('#' + modalId);
			if ($modal.length) {
				$modal.addClass('modal-active').fadeIn();
				$('body').css('overflow', 'hidden');
			}
		},

		/**
		 * Close modal
		 */
		closeModal: function() {
			$('.modal-active').fadeOut(function() {
				$(this).removeClass('modal-active');
			});
			$('body').css('overflow', 'auto');
		},

		/**
		 * Show notification
		 */
		showNotification: function(message, type) {
			type = type || 'info';

			var $notification = $('<div class="alert alert-' + type + '"></div>')
				.append('<span>' + message + '</span>')
				.append('<button class="alert-close">&times;</button>');

			$('body').prepend($notification);

			setTimeout(function() {
				$notification.fadeOut(function() {
					$(this).remove();
				});
			}, 5000);
		},

		/**
		 * Utilities
		 */
		utils: {
			/**
			 * Debounce function
			 */
			debounce: function(func, wait) {
				var timeout;
				return function() {
					var context = this;
					var args = arguments;
					clearTimeout(timeout);
					timeout = setTimeout(function() {
						func.apply(context, args);
					}, wait);
				};
			},

			/**
			 * Throttle function
			 */
			throttle: function(func, limit) {
				var inThrottle;
				return function() {
					var args = arguments;
					var context = this;
					if (!inThrottle) {
						func.apply(context, args);
						inThrottle = true;
						setTimeout(function() {
							inThrottle = false;
						}, limit);
					}
				};
			},

			/**
			 * Get color variable
			 */
			getColor: function(colorName) {
				return getComputedStyle(document.documentElement)
					.getPropertyValue('--color-' + colorName).trim();
			}
		}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		IGP.init();

		// Make available globally for console access
		window.IGP = IGP;
	});

	/**
	 * Smooth scroll polyfill for older browsers
	 */
	if (!('scrollBehavior' in document.documentElement.style)) {
		$.fn.smoothScroll = function(options) {
			options = options || {};
			var duration = options.duration || 800;

			return this.each(function() {
				var $this = $(this);
				var start = $this.offset().top;
				var target = start;

				$('html, body').animate({
					scrollTop: target
				}, duration);
			});
		};
	}

})(jQuery);
