

	var imperialCanvas = {

		init: function (canvasID)
		{


			//console.log("Called for canvas "+canvasID);

			var jQuery = function(id){return document.getElementById(id)};



			var canvas = this.__canvas = new fabric.Canvas('c_' + canvasID, {
				isDrawingMode: true

			});





			fabric.Object.prototype.transparentCorners = false;

			var drawingModeEl = jQuery('drawing-mode_' + canvasID),
			drawingOptionsEl = jQuery('drawing-mode-options_' + canvasID),
			drawingColorEl = jQuery('drawing-color_' + canvasID),
			drawingLineWidthEl = jQuery('drawing-line-width_' + canvasID),
			clearEl = jQuery('clear-canvas_' + canvasID),
			saveCanvas = jQuery('save-canvas_' + canvasID),
			redoCanvas = jQuery('redo-canvas_' + canvasID),
			undoCanvas = jQuery('undo-canvas_' + canvasID),
			textCanvas = jQuery('text-canvas_' + canvasID);
         moveModeCanvas = jQuery('move-mode_' + canvasID);





			// redo and undo functionality

			var isRedoing = false;
			var h = [];
			undoCanvas.onclick = function undo() {
					if(canvas._objects.length>0){
					h.push(canvas._objects.pop());
					canvas.renderAll();
				   }
			};

			redoCanvas.onclick = function redo() {
				if(h.length>0){
				isRedoing = true;
			   canvas.add(h.pop());
			  }
			};


			clearEl.onclick = function() { canvas.clear() };


         // If Drawing mode is clicked
			drawingModeEl.onclick = function()
         {

            console.log("Enter Drawing");


            canvas.isDrawingMode = true;
			};

         // If move mode is clicked
			moveModeCanvas.onclick = function()
         {

            console.log("Enter move");
            canvas.isDrawingMode = false;

			};



         // add text functionality
			textCanvas.onclick = function Addtext() {

            canvas.isDrawingMode = false;

            // Add Focus on Move mode tab

				canvas.add(new fabric.IText('Add some text', {
					  left: 50,
					  top: 100,
					  fontFamily: 'arial',
					  fill: '#333',
					fontSize: 20
				}));
			};





/*
         // If Drawing mode is clicked
			drawingModeEl.onclick = function() {


            canvas.isDrawingMode = !canvas.isDrawingMode;
				if (canvas.isDrawingMode) {
					drawingModeEl.innerHTML = 'Cancel drawing mode';
					drawingOptionsEl.style.display = '';
				}
				else {
					drawingModeEl.innerHTML = 'Enter drawing mode';
					drawingOptionsEl.style.display = 'none';
				}
			};

         */







			jQuery('drawing-mode-selector_' + canvasID).onchange = function()
			{
				canvas.freeDrawingBrush = new fabric[this.value + 'Brush'](canvas);
				if (canvas.freeDrawingBrush) {
					canvas.freeDrawingBrush.color = drawingColorEl.value;
					canvas.freeDrawingBrush.width = parseInt(drawingLineWidthEl.value, 10) || 1;
				}
			};


			drawingColorEl.onchange = function()
			{
				//console.log("Current Val = "+ canvas.freeDrawingBrush.color);
                //canvas.freeDrawingBrush.color = this.value;
                canvas.freeDrawingBrush.color = drawingColorEl.value;
                //canvas.freeDrawingBrush.color = '#00ff00';
				//console.log("New Val = "+ drawingColorEl.value);
			};

			drawingLineWidthEl.onchange = function() {
				canvas.freeDrawingBrush.width = parseInt(this.value, 10) || 1;
				this.previousSibling.innerHTML = this.value;
			};

			if (canvas.freeDrawingBrush) {
				canvas.freeDrawingBrush.color = drawingColorEl.value;
				canvas.freeDrawingBrush.width = parseInt(drawingLineWidthEl.value, 10) || 1;
			}

			saveCanvas.onclick = function() {
				// Get the JSON
				var json_data = JSON.stringify(canvas.toDatalessJSON());
				ajaxSaveDrawing(json_data, canvasID);
			};




			/* Load prevous saved picture */
			var savedJSON = document.getElementById("savedDrawing_"+canvasID).value;
			savedJSON = decodeURIComponent(savedJSON);
			canvas.loadFromJSON(savedJSON, canvas.renderAll.bind(canvas), function(o, object) {
				fabric.log(o, object);
			});


            //set initial colour
            canvas.freeDrawingBrush.color = drawingColorEl.value;

		}

	}
