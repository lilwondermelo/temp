var teamItemCount, teamItemStep = 0;
$(document).ready(function() {
    $('body').on('click', '.buttonTask', function() {
        addTaskForm();
    })

    $('body').on('click', '.menuItem', function() {
        var ind = $(this).index();
        switch (ind) {
        	case 0:
        		getTasks();
        		break;
        	case 1:
        		overlayOpen('addTask');
        		break;
        	case 2:
        		getBoards();
        		break;

        }
    })

    $('body').on('click', '.addFormTeamArrow', function() {
    	arrowClick($(this).attr('data-id'));
    })
    getBoards();
    $( function() {
	    $( "#datepicker" ).datepicker();
	  } );
    $( "#datepicker").focus();
    $( "body").on('change', '#datepicker', function() {
    	$(".addFormDate span").html($("#datepicker").val());
    })
    $('body').on('click', '.addFormDate', function() {
    	$( "#datepicker").focus();
    })
    $('body').on('click', '.overlayTitle', function() {
    	overlayClose();
    })
    $('body').on('click', '.boardsListItem:not(".boardsListItemAdd"), .addFormTeamItem:not(".addFormTeamItemAdd")', function() {
    	if ($(this).hasClass('active')){
    		$(this).removeClass('active');
    	}
    	else {
    		$(this).addClass('active');
    	}
    })

})


function listItemCheck() {

}

function addTaskForm() {
    $.ajax({
        type: "POST",
        url: "../core/_ajaxListener.class.php",
        data: {classFile: "../tasks/classes/application.class", class: "Application", method: "addTaskForm"
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            $('.overlay').html(data.data);
        } else {
           console.log(data);
        }
        teamItemCount = $('.addFormTeamItem').length;
    	checkArrows(teamItemStep);
    });
}

function checkArrows(step) {
	if (step > 0) {
		$('#teamArrowLeft').show();
	}
	else {
		$('#teamArrowLeft').hide();
	}
	if ((step+5 < teamItemCount) && (teamItemCount > 5)) {
		$('#teamArrowRight').show();
	}
	else {
		$('#teamArrowRight').hide();
	}
}

function arrowClick(step) {
	teamItemStep = +step+teamItemStep;
	$('.addFormTeamItem').css('transform', 'translateX(' + (-60*teamItemStep) + 'px)');
	checkArrows(teamItemStep);
	
}

function getBoards() {
	$.ajax({
        type: "POST",
        url: "../core/_ajaxListener.class.php",
        data: {classFile: "../tasks/classes/application.class", class: "Application", method: "getBoards"
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            $('.main').html(data.data);
        } else {
           console.log(data);
        }
        
    });
}


 function getTasks() {
        	$.ajax({
        type: "POST",
        url: "../core/_ajaxListener.class.php",
        data: {classFile: "../tasks/classes/application.class", class: "Application", method: "getTasks"
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            $('.main').html(data.data);
        } else {
           console.log(data);
        }
        
    });
}


function overlayOpen(type) {
	$('.menu').hide();
	$('.main').hide();
	switch (type) {
		case 'addTask':
			addTaskForm();
			break;
	}
	$('.overlay').show();
}


function overlayClose() {
	$('.overlay').hide();
	$('.menu').show();
	$('.main').show();
}