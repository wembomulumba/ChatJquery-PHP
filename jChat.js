/*
 *	jQuery Chat Plugin
 *	Ajax Chat plugin for jquery
 *	Version 1.5, April, 2016
 *	Released Under Envato Regular and Extended Licenses by Paulo Regina
 *  www.pauloreg.com
 */
 
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

(function($) {
 
    $.fn.extend({
        
		// Chat Plugin 
        Chat: function(options) 
		{
 			
			// Default Configurations
			var chat = {}
			
            var defaults = {
				ajaxMessage: 'includes/ajax_messages.php',
				ajaxDelete: 'includes/ajax_del_messages.php',
				ajaxSendMessage: 'includes/ajax_send.php',
				ajaxUpdate: 'includes/ajax_update.php',
				ajaxLastSeen: 'includes/ajax_last_seen.php',
				chat: '',
				waitRefresh: 800,
				chatRefresh: 2000,
				emoticonsModal: '#emoticons',
				
				realtime: true
            }
			
			var options =  $.extend(defaults, options);
			
			var opt = options;
			
			chat.renderChat = function()
							{

								$(opt.chat).html(chat.messages());

								$("ul.messages-layout").on('click', 'span.delete', function() 
							 	{	
									var message_id = $(this).attr("id");
									
									var construct = {messageID: message_id};
									
									$.post(opt.ajaxDelete, construct, function(response){});
									
									var client_or_server = $(this).parent().parent().parent().attr("class");
									var client_or_server_id = $(this).parent().parent().parent().attr("id");
									
									$("li."+client_or_server+'#'+client_or_server_id).remove();
								
								});
								
								// Handle Emoticons
								$(".send-group").delegate('[data-option="emotions"]', 'click', function(e) 
							 	{	
									$("#emoticons .modal-body").html(
										'<img class="emoticons" src="images/emoticons/Angry.png" id="angry" data-value=":@">' + 
										'<img class="emoticons" src="images/emoticons/Balloon.png" id="balloon" data-value="[balloon]">' +
										'<img class="emoticons" src="images/emoticons/Big-Grin.png" id="big-grin" data-value="[big-grin]">' +
										'<img class="emoticons" src="images/emoticons/Bomb.png" id="bomb" data-value="[bomb]">' +
										'<img class="emoticons" src="images/emoticons/Broken-Heart.png" id="broken-heart" data-value="[broken-heart]">' +
										'<img class="emoticons" src="images/emoticons/Cake.png" id="cake" data-value="[cake]">' +
										'<img class="emoticons" src="images/emoticons/Cat.png" id="cat" data-value="[cat]">' +
										'<img class="emoticons" src="images/emoticons/Clock.png" id="clock" data-value="[clock]">' +
										'<img class="emoticons" src="images/emoticons/Clown.png" id="clown" data-value="[clown]">' +
										'<img class="emoticons" src="images/emoticons/Cold.png" id="cold" data-value="[cold]">' +
										'<img class="emoticons" src="images/emoticons/Confused.png" id="confused" data-value="[confused]">' +
										'<img class="emoticons" src="images/emoticons/Cool.png" id="cool" data-value="[cool]">' +
										'<img class="emoticons" src="images/emoticons/Crying.png" id="crying" data-value="[crying]">' +
										'<img class="emoticons" src="images/emoticons/Crying2.png" id="crying2" data-value="[crying2]">' +
										'<img class="emoticons" src="images/emoticons/Dead.png" id="dead" data-value="[dead]">' +
										'<img class="emoticons" src="images/emoticons/Devil.png" id="devil" data-value="[devil]">' +
										'<img class="emoticons" src="images/emoticons/Dizzy.png" id="dizzy" data-value="[dizzy]">' +
										'<img class="emoticons" src="images/emoticons/Dog.png" id="dog" data-value="[dog]">' +
										'<img class="emoticons" src="images/emoticons/Don\'t-tell-Anyone.png" id="dont-tell-anyone" data-value="[dont-tell-anyone]">' +
										'<img class="emoticons" src="images/emoticons/Drinks.png" id="drinks" data-value="[drinks]">' +
										'<img class="emoticons" src="images/emoticons/Drooling.png" id="drooling" data-value="[drooling]">' +
										'<img class="emoticons" src="images/emoticons/Flower.png" id="flower" data-value="[flower]">' +
										'<img class="emoticons" src="images/emoticons/Ghost.png" id="ghost" data-value="[ghost]">' +
										'<img class="emoticons" src="images/emoticons/Gift.png" id="gift" data-value="[gift]">' +
										'<img class="emoticons" src="images/emoticons/Girl.png" id="girl" data-value="[girl]">' +
										'<img class="emoticons" src="images/emoticons/Goodbye.png" id="goodbye" data-value="[goodbye]">' +
										'<img class="emoticons" src="images/emoticons/Heart.png" id="heart" data-value="[heart]">' +
										'<img class="emoticons" src="images/emoticons/Hug.png" id="hug" data-value="[hug]">' +
										'<img class="emoticons" src="images/emoticons/Kiss.png" id="kiss" data-value="[kiss]">' +
										'<img class="emoticons" src="images/emoticons/Laughing.png" id="laughing" data-value="[laughing]">' +
										'<img class="emoticons" src="images/emoticons/Ligthbulb.png" id="lightbulb" data-value="[lightbulb]">' +
										'<img class="emoticons" src="images/emoticons/Loser.png" id="loser" data-value="[loser]">' +
										'<img class="emoticons" src="images/emoticons/Love.png" id="love" data-value="[love]">' +
										'<img class="emoticons" src="images/emoticons/Mail.png" id="mail" data-value="[mail]">' +
										'<img class="emoticons" src="images/emoticons/Music.png" id="music" data-value="[music]">' +
										'<img class="emoticons" src="images/emoticons/Nerd.png" id="nerd" data-value="[nerd]">' +
										'<img class="emoticons" src="images/emoticons/Night.png" id="night" data-value="[night]">' +
										'<img class="emoticons" src="images/emoticons/Ninja.png" id="ninja" data-value="[ninja]">' +
										'<img class="emoticons" src="images/emoticons/Not-Talking.png" id="not-talking" data-value="[not-talking]">' +
										'<img class="emoticons" src="images/emoticons/on-the-Phone.png" id="on-the-phone" data-value="[on-the-phone]">' +
										'<img class="emoticons" src="images/emoticons/Party.png" id="party" data-value="[party]">' +
										'<img class="emoticons" src="images/emoticons/Pig.png" id="pig" data-value="[pig]">' +
										'<img class="emoticons" src="images/emoticons/Poo.png" id="poo" data-value="[poo]">' +
										'<img class="emoticons" src="images/emoticons/Rainbow.png" id="rainbow" data-value="[rainbow]">' +
										'<img class="emoticons" src="images/emoticons/Rainning.png" id="rainning" data-value="[rainning]">' +
										'<img class="emoticons" src="images/emoticons/Sacred.png" id="sacred" data-value="[sacred]">' +
										'<img class="emoticons" src="images/emoticons/Sad.png" id="sad" data-value=":(">' +
										'<img class="emoticons" src="images/emoticons/Scared.png" id="scared" data-value="[scared]">' +
										'<img class="emoticons" src="images/emoticons/Sick.png" id="sick" data-value="[sick]">' +
										'<img class="emoticons" src="images/emoticons/Sick2.png" id="sick2" data-value="[sick2]">' +
										'<img class="emoticons" src="images/emoticons/Silly.png" id="silly" data-value="[silly]">' +
										'<img class="emoticons" src="images/emoticons/Sleeping.png" id="sleeping" data-value="[sleeping]">' +
										'<img class="emoticons" src="images/emoticons/Sleeping2.png" id="sleeping2" data-value="[sleeping2]">' +
										'<img class="emoticons" src="images/emoticons/Sleepy.png" id="sleepy" data-value="[sleepy]">' +
										'<img class="emoticons" src="images/emoticons/Sleepy2.png" id="sleepy2" data-value="[sleepy2]">' +
										'<img class="emoticons" src="images/emoticons/smile.png" id="smile" data-value=":)">' +
										'<img class="emoticons" src="images/emoticons/Smoking.png" id="smoking" data-value="[smoking]">' +
										'<img class="emoticons" src="images/emoticons/Smug.png" id="smug" data-value="[smug]">' +
										'<img class="emoticons" src="images/emoticons/Stars.png" id="stars" data-value="[stars]">' +
										'<img class="emoticons" src="images/emoticons/Straight-Face.png" id="straight-face" data-value="[straight-face]">' +
										'<img class="emoticons" src="images/emoticons/Sun.png" id="sun" data-value="[sun]">' +
										'<img class="emoticons" src="images/emoticons/Sweating.png" id="sweating" data-value="[sweating]">' +
										'<img class="emoticons" src="images/emoticons/Thinking.png" id="thinking" data-value="[thinking]">' +
										'<img class="emoticons" src="images/emoticons/Tongue.png" id="tongue" data-value="[tongue]">' +
										'<img class="emoticons" src="images/emoticons/Vomit.png" id="vomit" data-value="[vomit]">' +
										'<img class="emoticons" src="images/emoticons/Wave.png" id="wave" data-value="[wave]">' +
										'<img class="emoticons" src="images/emoticons/Whew.png" id="whew" data-value="[whew]">' +
										'<img class="emoticons" src="images/emoticons/Win.png" id="win" data-value="[win]">' +
										'<img class="emoticons" src="images/emoticons/Winking.png" id="winking" data-value="[winking]">' +
										'<img class="emoticons" src="images/emoticons/Yawn.png" id="yawn" data-value="[yawn]">' +
										'<img class="emoticons" src="images/emoticons/Yawn2.png" id="yawn2" data-value="[yawn2]">' +
										'<img class="emoticons" src="images/emoticons/Zombie.png" id="zombie" data-value="[zoombie]">'
									);
										
									$(".emoticons").on('click', function()
									{
										var id = $(this).attr('id');
										var image_url = $(this).data('value');
										var current_value = $("input#text-input-field").val();
										
										$("input#text-input-field").val(current_value+image_url);
										$(opt.emoticonsModal).modal('hide');
										
										// clean stuff to avoid duplication
										image_url = $(this).data('value').val('');
									});
	
								});
																
							}
			
			chat.messages = function() 
							  {
								var construct = {clientID: 'set'}
								
								$.post(opt.ajaxMessage, construct, function(response) 
								{
									if(response)
									{
										$("ul.messages-layout").html(response);
										
										toScroll = $("ul.messages-layout");
										$("ul.messages-layout").animate({ scrollTop: toScroll[0].scrollHeight }, 'fast');
									} 
								});	
																
							  }
			
			chat.sendOnPress = function()
								{
									$('#text-input-field').keypress(function(e)
									{
										if(e.which == 13)
										{
											chat.sendMessage();	
										}
									});
								}
			
			chat.sendOnClick = function()
								{
									$("#sendMessage").on('click', function(e) 
							 		{	
										chat.sendMessage();
									});
								}
			
			chat.inputHandler = function()
								{
									chat.sendOnPress();
									chat.sendOnClick();
								}
			
			chat.update = function()
						  {
							var construct = {update: 'true', id: getParameterByName('id')}
							
							$.post(opt.ajaxUpdate, construct, function(response)
							{   
								if(response.length !== 0)
								{
									response = $.parseJSON(response);
								
									last_message = parseInt( $('ul.messages-layout').find('li:last-child').attr('id') ) || 0; 
									
									last_message_server = response.last_message;
									
									if(last_message_server > last_message)
									{
										$("ul.messages-layout").append(response.message);
										
										$(document).prop('title', 'jChat ' + response.new_messages);
										
										toScroll = $("ul.messages-layout");
										$("ul.messages-layout").animate({ scrollTop: toScroll[0].scrollHeight }, 'fast');
									}
								}
							});

						  }
			
			chat.sendMessage = function()
							 {
								var message_entry = $('#text-input-field').val();
								var construct = { message: message_entry, id: getParameterByName('id') };
								
								$.post(opt.ajaxSendMessage, construct, function(response)
								{
									setTimeout(function() {
										$("ul.messages-layout").append(response);
										
										toScroll = $("ul.messages-layout");
										$("ul.messages-layout").animate({ scrollTop: toScroll[0].scrollHeight }, 'fast');
									
									}, opt.waitRefresh);
																		
									$('#text-input-field').val('');
								});
																
							 }
			
			chat.lastSeen = function()
									{
										$(window).bind("beforeunload", function(){
											$.post(opt.ajaxLastSeen, 'offline=true', function(response){}); 	
										}); 
										
										$(window).focus(function() {
											$.post(opt.ajaxLastSeen, 'offline=false', function(response){}); 
										});
									}
								
			chat.polling = function() 
							{
								if(opt.realtime === true)
								{
									chat.interval = setInterval(function() {
									  chat.update();
									}, opt.chatRefresh);
								}
							}
			
			chat.stopPolling = function()
								{
									clearInterval(chat.interval);
									chat.interval = 0;
								}
				
			chat.renderChat();
									
			chat.inputHandler();
			
			chat.polling();
			
			chat.lastSeen();

        } // end chat
		
    }); // end extend
     
})(jQuery);