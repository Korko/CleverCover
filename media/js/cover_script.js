/**
 * This script can generate from a global picture two parts in order to be used as cover and profile picture in Facebook.
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <jeremy.lemesle@korko.fr> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return. Jeremy Lemesle
 * ----------------------------------------------------------------------------
 */

// TODO :
// Possibilité 2 images
// ++ Changer directement sur FB ou G+
// ++ Pouvoir tourner l'image

// required : width, height, id
var ResizableCanvas = (function() {
	var poolEventId = 0;

	return function(params) {

		/*********************
		 *	 Variables	 *
		 ********************/
		this._id = params.id;
		this._width = params.width;
		this._height = params.height;
		this._extractRatio = params.extractRatio || 1;

		this._links = {};
		this._fullWidth = params.width;
		this._fullHeight = params.height;
		this._left = 0;
		this._top = 0;
		this._right = 0;
		this._bottom = 0;
		this._maxWeight = params.weight;

		this._img = null;
		this._x = 0;
		this._y = 0;
		this._ratio = 100;
		this._minRatio = 0;
		this._opacity = 1;
		this._flipFactor = 1;
		this._blurFactor = 0;
		this._colorMask = "#000";
		this._colorMaskOpacity = 0;

		this._events = {};

		/*********************
		 *	  Methods	  *
		 ********************/

		this._draw = function(context, subratio, xgap, ygap) {
			context.clearRect(0, 0, this._fullWidth, this.fullHeight);
			context.save();
			context.globalAlpha = this._opacity;

			subratio = subratio || 1;
			context.scale(this._ratio / subratio, this._ratio / subratio);

			if(this._flipFactor === -1) {
				context.translate(this._fullWidth/this._ratio, 0);
				context.scale(-1, 1);
			}

			xgap = xgap || 0;
			ygap = ygap || 0;
			context.drawImage(this._img, this._flipFactor*((-this._x-this._left)/this._ratio+xgap), (-this._y-this._top)/this._ratio+ygap);

			context.restore();

			context.fillStyle = this._colorMask;
			context.globalAlpha = this._colorMaskOpacity / 100;
			context.fillRect(0, 0, this._fullWidth, this._fullHeight);

                        if(this._blurFactor > 0) {
                                stackBlurCanvasRGB(this._id, 0, 0, this._fullWidth, this._fullHeight, this._blurFactor);
                        }
		};

		// draw the canvas
		this.draw = function(propagate) {
			var _draw = function() {
				this._fixPosition();
				this._draw(ctx(this._id));
			};

			if (propagate) {
				this._propagate(_draw, [], true);
			} else {
				_draw.apply(this);
			}
		};

		this._prepare = function() {
			$(this._id).setAttribute('width', this._width);
			$(this._id).setAttribute('height', this._height);
			$(this._id).style.width = this._width + 'px';
			$(this._id).style.height = this._weight + 'px';
		};
		jQuery(this._prepare.bind(this));

		// link to an other ResizableCanvas
		this.link = function(canvas, params) {
			if(this._links[canvas._id]) {
				return;
			}

			var shared = ["left", "top"];
			for(i in shared) {
				if(params[shared[i]] < 0) {
					canvas['_' + shared[i]] -= params[shared[i]];
				} else {
					this['_' + shared[i]] += params[shared[i]] || 0;
				}
			}

			this._links[canvas._id] = canvas;
			canvas._links[this._id] = this;

			// TODO: adapt to multiple links
			this._fullWidth = Math.max(this._width + Math.max(0, canvas._left + canvas._width - this._width), canvas._width + Math.max(0, this._left + this._width - canvas._width));
			this._fullHeight = Math.max(this._height + Math.max(0, canvas._top + canvas._height - this._height), canvas._height + Math.max(0, this._top + this._height - canvas._height));
			this._propagate({
				_fullWidth : this._fullWidth,
				_fullHeight : this._fullHeight
			});
			jQuery(this._prepare.bind(this));
		};

		this._propagate = function(event, params, execute) {
			var values = [];
			if(!event.id) {
				event.id = ++poolEventId;
			} else if(this._events[event.id]) {
				return values;
				// Already done and forwarded
			} else {
				execute = true;
			}

			if(execute) {
				if(jQuery.isFunction(event)) {
					values.push(event.apply(this, params));
				} else {
					// Treat the event
					for(var index in event) {
						this[index] = event[index];
					}
				}
			}

			this._events[event.id] = event;

			// Forward
			for(var canvasId in this._links) {
				jQuery.merge(values, this._links[canvasId]._propagate(event, params));
			}
			return values;
		};

		// change canvas picture
		this.setImage = function(url, callback) {
			this._img = loadImg(url, function(e) {
				var success = true;
				if(e.type === 'error') {
					alert('This picture is not available.');
					success = false;
				} else if(this._img.naturalWidth < this._fullWidth || this._img.naturalHeight < this._fullHeight) {
					alert('This picture is too small. Need at least ' + this._fullWidth + 'x' + this._fullHeight + 'pixels (got ' + this._img.naturalWidth + 'x' + this._img.naturalHeight+').');
					success = false;
				} else {
					this._minRatio = Math.max(this._fullWidth / this._img.naturalWidth, this._fullHeight / this._img.naturalHeight) * 100;

					this._propagate({
						_img : this._img,
						_minRatio : this._minRatio
					});

					this.changeRatio(this._ratio);
				}(callback || noop)(success);
			}.bind(this), true);
		};

		this.move = function(data) {
			this._propagate(function() {
				this._x -= data.dx;
				this._y -= data.dy;
				this.draw(false);
			}, [], true);
		};

		this._fixPosition = function() {
			this._x = mm(0, this._x, this._flipFactor * (this._img.naturalWidth - this._fullWidth / this._ratio) * this._ratio);
			this._y = mm(0, this._y, (this._img.naturalHeight - this._fullHeight / this._ratio) * this._ratio);
		};

		this.getRatio = function() {
			return (this._ratio * 100 - this._minRatio) / (100 - this._minRatio) * 100;
		};

		this.changeRatio = function(newRatio) {
			newRatio = mm(0, newRatio, 100);

			this._propagate(function() {
				// Au final, cette valeur [0,100] doit être transformer.
				// En effet, la plus petite valeur est celle qui affiche l'image dans toute sa largeur (exactement celle de la couverture)
				this._ratio = this._minRatio + newRatio * (100 - this._minRatio) / 100;
				// ratio est compris dans [min, 100]
				this._ratio /= 100;

				this.draw();
			}, [], true);
		};

		this.save = function(callback) {
			return this._propagate(function() {
				var saveCanvas = $(this._id);

				if(this._extractRatio !== 1) {
					saveCanvas = c('canvas', {
						attributes : {
							width : this._width * this._extractRatio,
							height : this._height * this._extractRatio
						}
					});

					this._draw(saveCanvas.getContext('2d'), 1/this._extractRatio);
				}

				(function(canvas, maxWeight, callback) {
					(function loopfn(compression, oldSize) {
	                                        // L'image de la couverture que nous pourrons sauvegarder est en fait une chaîne sous forme base64
        	                                // e.g. data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAYAAAA9zQYyAAAgA...
                	                        // Fonctionnalité permise par un canvas si l'origine de l'image est sûre (même domaine) d'où le fichier php "img.php" plus bas
                        	                data = saveCanvas.toDataURL('image/jpeg', compression);
                                	        (this._maxWeight && getImageWeight(data, function(size) {
							if(size !== oldSize && size > maxWeight) {
								loopfn(compression + 0.1, size);
							} else {
								callback(data);
							}
						})) || callback(data);
					})();
				})(saveCanvas, this._maxWeight, callback);
			}, [], true);
		};

		this.flip = function() {
			this._propagate(function() {
				this._flipFactor = this._flipFactor === -1 ? 1 : -1;
				this.draw();
			}, [], true);
		};

		this.blur = function(factor) {
			this._blurFactor = factor;
			this.draw();
		};

		this.colorMask = function(color, opacity) {
			this._propagate(function() {
				this._colorMask = color;
				this._colorMaskOpacity = opacity;
				this.draw();
			}, [], true);
		};
	};
})();

/**
 * Voici LA classe qui gère tout l'affichage de la couverture et de l'image
 * A l'origine les deux ne faisaient parti que d'un seul et unique <canvas> mais malheureusement
 * Il n'est pas possible d'extraire seulement une parti d'un canvas sous forme d'image
 * Alors voici 2 canvas différents : un pour la couverture et l'autre pour l'image.
 */
window.cleverCover = (function() {

	var params = {
		cover : {},
		avatar : {},
		link : {},
		type : null
	};

	return {
		init : function(type, url_cover, url_avatar, callback, special) {
			var splited = !!url_avatar,
				slider_choice = 'cover',
				canvas = {};

			switch(type) {
				case 'facebook':
					params.cover = {
						width : 851,
						height : 315,
						weight : 100
					};
					params.avatar = {
						width : 160,
						height : 160,
						extractRatio : 180/160
					};
					params.link = !special ? {
						left : 20,
						top : 177
					} : {
						left : 18,
						top : 228
					};
					break;

				case 'twitter':
					params.cover = {
						width : 520,
						height : 260
					};
					params.avatar = {
						width : 73,
						height : 73
					};
					params.link = {
						left : 223,
						top : 24
					};
					break;
			}
			params.type = type;

			canvas['cover'] = new ResizableCanvas(jQuery.extend({
				id : 'canvas_cover'
			}, params.cover));
			canvas['avatar'] = new ResizableCanvas(jQuery.extend({
				id : 'canvas_picture'
			}, params.avatar));

			if(!splited) {
				canvas['avatar'].link(canvas['cover'], params.link);
			}

			jQuery((function($) {
				$('#content').addClass(splited ? 'splited' : '');
				$('#content_inner').addClass(type+' '+(special ? 'special' : ''));
				$('#save').click(function() {
					var data = [];
					canvas['cover'].save(function(coverData) {
						data.push(coverData);
						(!splited || data.length === 2) && fncb();
					});
					if(splited) {
						canvas['avatar'].save(function(avatarData) {
							data.push(avatarData);
							data.length === 2 && fncb();
						});
					}

					function fncb() {
						var div = $('<div>');
						$.each(data, function(id, data) {
							var node = c('img');
							node.style.height = '100px';
							node.setAttribute('src', data);
							$(node).bind('click', function() {
								jQuery.download('img.php', 'src='+data);
							});
							div.append(node);
						});

						var content = $('<p>Here\'s your avatar and your cover, just save them by clicking on each one and put them directly to your favorite social network.</p>');
						content = content.add(div);
						popup.content(content, true, 'cover_save');
					}
				});
                        	$('input[name="cover_ratio"]').on('change', function() {
                                	canvas[slider_choice].changeRatio($(this).val());
                        	});
                                $('input[name="cover_blur"]').on('change', function() {
                                        canvas[slider_choice].blur($(this).val());
                                });
				$('input[name="cover_choice"]').change(function() {
					slider_choice = $(this).val();
				});
                        	$('input[name="cover_flip"]').change(function() {
                                	canvas[slider_choice].flip();
                        	});
				$('input[name="color_mask"],input[name="color_mask_opacity"]').change(function() {
					canvas[slider_choice].colorMask($('input[name="color_mask"]').val(), $('input[name="color_mask_opacity"]').val());
				});
			})(jQuery));

			var manageSuccess = (function() {
				var globSuccess;
				return function(success) {
					if(!splited) {
						callback(success);
					} else {
						if(globSuccess !== undefined) {
							callback(globSuccess && success);
						} else {
							globSuccess = success;
						}
					}
				};
			})();

			var globSuccess;
			canvas['cover'].setImage(url_cover, function(success) {
				if(success) {
					var fn = function(data) {
						canvas['cover'].move(data);
					};
					jQuery('#canvas_cover').drag(fn);
					if(!splited) {
						jQuery('#canvas_picture').drag(fn);
					}
				}
				manageSuccess(success);
			});
			if(splited) {
				canvas['avatar'].setImage(url_avatar, function(success) {
					if(success) {
						jQuery('#canvas_picture').drag(function(data) {
							canvas['avatar'].move(data);
						}).hover(function() {
							canvas['avatar'].expand();
						}, function() {
							canvas['avatar'].reduce()
						});
					}
					manageSuccess(success);
				});
			}
		},
		help: function() {
			jQuery('#overlay').show();
		}
	};
})();

if(!jQuery.support.input_slide) {
	jQuery(function($){
        	// loop through all <input type=range/>
        	// on the page
        	$("input[type=range]").each(function(){
            		var range = $(this);
                	
			// Create <div/> to hold jQuery UI Slider
            		var sliderDiv = $("<div/>");
            		sliderDiv.width(range.width());
			            
            		// Insert jQuery UI Slider where the
            		// <input type=range/> is located
            		range.after(
                		sliderDiv.slider({
                    			// Set values that are set declaratively
                    			// in the <input type=range/> element
                    			min: parseFloat(range.attr("min")),
                    			max: parseFloat(range.attr("max")),
                    			value: parseFloat(range.val()),
                    			step: parseFloat(range.attr("step")),
                    			// Update the <input type=range/> when
                    			// value of slider changes
                    			slide: function(evt, ui) {
                        			range.val(ui.value);
                    			},
                    			change: function(evt, ui) {
                        			// set <input type=range/> value
                        			range.val(ui.value);
						range.change();
                    			}
        			})
        		).
        		// Hide <input type=range/> from display
        		hide();
        	});
	});
}

if(!jQuery.support.canvas) {
	alert("I'm sorry but your browser seems not to be able to support CleverCover. Please use Chrome or Firefox instead. Thanks.");
	throw "exit";
}
