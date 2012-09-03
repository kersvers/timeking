/* Author:
  Rene Sijnke
  http://www.krsvrs.nl
*/

var data;
  
var lines = {
  0: [
    {"text": "Damn! This guy is practically running the studio on his own!"},
    {"text": "Me gusta registro de horas!"}
  ],
  1: [
    {"text": "Doing OK. But hes got to step up his game!"},
    {"text": "Running the hours like a sir"}
  ],
  2: [
    {"text": "Y U NO WORK HARDER? Need coffee?"},
    {"text": "Bitch please, do you even work?"}
  ],
  3: [
    {"text": "Y U NO WORK HARDER? Need coffee?"},
    {"text": "Bitch please, do you even work?"}
  ]
};
  
$(function(){
	
	$.post("inc/get_ranking.php", data, function(data) {
		
		$('#container').addClass('in');
		$('#preloader').stop().animate({opacity: 0}, 300);
		
		if (data.succes) {
		  
		  timer_count($('.logged_hours'), Math.round(data.hours_total_registered));
		  
		  $('.hours_togo strong').html(Math.round(data.hours_total_month-data.hours_total_registered));
		  $('#hours_productive h4').html(Math.round((data.hours_total_registered / data.hours_until_today)*100));
		  
		  $('#progress').delay(400).animate({width: ((Math.round(data.hours_total_registered) / (data.hours_total_month)) * 390) + 'px'}, 1000, 'easeOutExpo');
		
		  for(var i = 0; i < data.ranking.length; i++){
  		  
  		  // clone the existing markup
  		  var _item = $('li.hide').clone();
  		  _item.removeClass('hide');
  		  
  		  // add an avatar
  		  _item.find('.user_avatar_holder').html($('<img src="img/avatar-'+data.ranking[i].name.toLowerCase().replace(/\s/g,'-')+'.png">'));
  		  
  		  // Goalies gets the moustache / hat
  		  if(data.ranking[i].group == "B-goalie") _item.find('.user_avatar').prepend($('<figure class="sir"></figure>'));
  		  
  		  // sets the ranking numbers and name
  		  _item.find('.rank').addClass('bg'+i).html(i+1);
  		  //_item.find('.rank').addClass(data.ranking[i].group);
  		  //_item.find('h2').html(data.ranking[i].name+'.');
  		  
  		  // winner and loser get a custom text, everybody else the default text
  		  if(i == 0) _item.find('.hours').html('Already <span>'+data.ranking[i].hours_registered+' hours logged</span>. Like a boss!');
  		  else if(i == data.ranking.length-1) _item.find('.hours').html('Only <span>'+data.ranking[i].hours_registered+' hours logged</span>. What a whimp!');
  		  else _item.find('.hours').html('<span>'+data.ranking[i].hours_registered+' hours logged</span>.');
  		  
  		  _item.find('.hours').append(' '+data.ranking[i].performance+'% productivity');
  		  
  		  // get a 'funny' sentence from the lines object
  		  _item.find('.desc').html(lines[i][Math.floor((Math.random()*lines[i].length))].text);

  		  // append it
  		  $('#user_ranking ul').append(_item);
  		  
		  }
			
			
		} else {
				
		}
	},"json");
	
});

function timer_count(target, amount) {
  
  var _labelAnimDiv = $('<div />');

	var currentLabel = Number(0);
	_labelAnimDiv.css('text-indent', currentLabel);
	
	_labelAnimDiv.stop();
	
	_labelAnimDiv.animate({
		'text-indent': amount + 'px'
	},
	{
		duration: 2500,
		easing: 'easeInOutCirc',
		step: function(now, fx) {
			target.text(Math.round(now));
		}
	});
  
}
