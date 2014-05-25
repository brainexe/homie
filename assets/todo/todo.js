/*
 * @author Shaumik "Dada" Daityari
 * @copyright December 2013
 */

App.Todo = {};

App.Todo.init = function(data) {
	var options = {
		todoTask: "todo-task",
		todoHeader: "task-header",
		todoDate: "task-date",
		todoDescription: "task-description",
		taskId: "task-",
		formId: "todo-form",
		dataAttribute: "data",
		deleteDiv: "delete-div"
	};

	var stati = {
		"pending" : "#pending",
		"progress" : "#progress",
		"completed" : "#completed"
	};

	// Add Task
	var generateElement = function(params) {
		var parent = $(stati[params.status]);

		if (!parent) {
			return;
		}

		var wrapper = $("<div />", {
			"class" : options.todoTask,
			"id" : options.taskId + params.id,
			"data" : ""+params.id
		}).appendTo(parent);

		$("<div />", {
			"class" : options.todoHeader,
			"text": params.name
		}).appendTo(wrapper);

		$("<div />", {
			"class" : options.todoDate,
			"text": params.date
		}).appendTo(wrapper);

		$("<div />", {
			"class" : options.todoDescription,
			"text": params.description
		}).appendTo(wrapper);

		wrapper.draggable({
			start: function() {
				$("#" + options.deleteDiv).show();
			},
			stop: function() {
				$("#" + options.deleteDiv).hide();
			},
			revert: "invalid",
			revertDuration : 200
		});

	};

	// initialize list
	$.each(data, function (index, params) {
		generateElement(params);
	});

	// Adding drop function to each category of task
	$.each(stati, function (index, value) {
		$(value).droppable({
			drop: function (event, ui) {
				var element = ui.helper,
					css_id = element.attr("id"),
					id = css_id.replace(options.taskId, ""),
					object = data[id];

				// Removing old element
				removeElement(object);

				// Changing object code
				object.status = index;

				// Generating new element
				generateElement(object);

				$.post('/todo/edit/', {
					id: id,
					changes: {
						status: index
					}
				});

				// Updating Local Storage
				data[id] = object;

				// Hiding Delete Area
				$("#" + options.deleteDiv).hide();
			}
		});
	});

	// Adding drop function to delete div
	$("#" + options.deleteDiv).droppable({
		drop: function(event, ui) {
			var element = ui.helper,
				css_id = element.attr("id"),
				id = css_id.replace(options.taskId, ""),
				object = data[id];

			// Removing old element
			removeElement(object);

			$.post('/todo/delete/', {'id':id});

			delete data[id];

			// Hiding Delete Area
			$("#" + options.deleteDiv).hide();
		}
	});

	// Remove task
	var removeElement = function (params) {
		$("#" + options.taskId + params.id).remove();
	};

	App.Todo.add = function() {
		var inputs = $("#" + options.formId + " :input"),
			errorMessage = "name can not be empty",
			name, description, date, tempData;

		if (inputs.length !== 4) {
			return;
		}

		name = inputs[0].value;
		description = inputs[1].value;
		date = inputs[2].value;

		if (!name) {
			alert(errorMessage);
			return;
		}

		tempData = {
			name: name,
			deadline: date,
			description: description
		};

		$.post('/todo/add/', tempData, function(result) {
			data[result.id] = result;
			generateElement(result);
		});

		// Reset Form
		inputs[0].value = "";
		inputs[1].value = "";
		inputs[2].value = "";
	};

	App.Todo.clear = function () {
		//TODO
	};

};