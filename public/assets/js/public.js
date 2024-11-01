(function( $ ) {
	'use strict';

	/**
	 * Public javascript for the support-chat
	 *
	 * @link       http://www.weedesign.de/weebotLite.html
	 * @since      1.0.0
	 *
	 * @package    weebotLite
	 * @subpackage weebotLite/public/assets
	 */
	
	/*
	*	run the support chat
	*/
	$( window ).load(function() {
		weebotLite.run();
	});
	
	var weebotLite = new function() {
	
		// running interval for checking answers to supported questions
		this.checkSupportInterval = false;
		// running interval for checking answers to fallbacked questions
		this.checkFallbackInterval = false;
		// clientside regex for checking email-addresses
		this.checkEmail = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
		// mouse position
		this.currentMousePos = { x: -1, y: -1 };
		
		/*
		*	this function runs the support chat
		*/
		this.run = function() {
			$.ajax({
				type: "POST",
				url: weebotLiteAJAX.ajaxurl,
				data: {
					action: "weebotLiteAJAX",
					method: "init"
				},
				xhrFields: {
					withCredentials: true
				},
				success: function(data){
					var options = JSON.parse(data);
					var chat = false;
					if($("#weebotLite-chat-settings").length>0) {
						if($("#weebotLite-chat-settings").val()==1) {
							chat = true;
						}
					}
					
					if( ( options["chat"] == 1 && options["footer"]==1 ) || chat == true ) {
					
						$.ajax({
							type: "POST",
							url: weebotLiteAJAX.ajaxurl,
							data: {
								action: "weebotLiteAJAX",
								method: "init:footer"
							},
							xhrFields: {
								withCredentials: true
							},
							success: function(data){
							
								$(document.body).append(data);
								
								if(options["chat:width"]!="") {
									$("#weebotLite, #weebotLite .chat").css("width",options["chat:width"]);
								}
								
								var loader = '<div id="weebotLite-loader" class="weebotLite-loader">';
								for(var i=1;i<9;i++) {
									loader+= '<div class="loader" id="weebotLite-loader-'+i+'"></div>';
								}
								loader+= '</div>';
								
								$("#weebotLite .small, #weebotLiteBackground").bind("click",function() {

									if($("#weebotLite .extended").hasClass("visible")) {
										$("#weebotLite, #weebotLite .extended, #weebotLiteBackground").removeClass("visible");
										$("#weebotLite .small").removeClass("open");
									} else {
										$("#weebotLite, #weebotLite .extended, #weebotLiteBackground").addClass("visible");
										$("#weebotLite .small").addClass("open");
										$("#weebotLite textarea").keypress(function(e) {
											if(e.keyCode==13) {
												$("#weebotLite .button.send").trigger("click");
												return false;
											}
										});
										$("#weebotLite textarea").keyup(function(e) {
											if(e.keyCode!=13) {
												$(this).css("height",(this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth")))+"px");
											}
										});
										$("#weebotLite textarea").focus(function(e) {
											window.setTimeout(function(){
												$("#weebotLite").addClass("text-focused");
											},100);
										});
										$("#weebotLite textarea").blur(function(e) {
											window.setTimeout(function(){
												$("#weebotLite").removeClass("text-focused");
											},100);
										});
										$("#weebotLite .chat").nanoScroller();
										$("#weebotLite .chat").nanoScroller({ scroll: 'bottom' });
										if($("#weebotLite .chat .content .more").length>0) {
											$("#weebotLite .chat .content").bind("scroll",function() {
												if($(this).scrollTop()==0) {
													var more = $("#weebotLite .chat .content .more");
													if(more.length>0) {
														if(!$(more).hasClass("loading")) {
															$(more).append(loader).addClass("loading");
															var start = $(more).data("start");
															$.ajax({
																type: "POST",
																url: weebotLiteAJAX.ajaxurl,
																data: {
																	action: "weebotLiteAJAX",
																	method: "user:log",
																	start: start
																},
																xhrFields: {
																	withCredentials: true
																},
																success: function(data){
																	$(more).removeClass("more").html(data);
																	$("#weebotLite .chat").nanoScroller();
																	weebotLite.email();
																}
															});
														}
													}
												}
											});
										}
			
										weebotLite.email();
										
										$("#weebotLite .settings .close").bind("click",function() {
											$("#weebotLite .settings").addClass("hidden");
										});
										
										$("#weebotLite .settings .save").bind("click",function() {
											if(weebotLite.checkEmail.test($("#weebotLite-user-email").val())===false) {
												$("#weebotLite-user-email").addClass("error").bind("keyup",function() {
													$(this).removeClass("error").unbind("keyup");
												});
											} else {
												if(!$("#weebotLite .settings").hasClass("hidden")) {
													$("#weebotLite .settings").addClass("hidden");
												}
												weebotLite.settingsEmail();
											}
										});
										
										if(weebotLite.checkSupportInterval===false) {
											weebotLite.checkSupportInterval = setInterval(weebotLite.checkSupport, 3000);
										}
										if(weebotLite.checkFallbackInterval===false) {
											weebotLite.checkFallbackInterval = setInterval(weebotLite.checkFallback, 30000);
										}
									}
								});
								
								/*
								 *	Send Function
								 */
								$("#weebotLite .extended .button.send").bind("click",function() {
								
									var text = $("#weebotLite textarea").val();

									var textHTML = text.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
									   return '&#'+i.charCodeAt(0)+';';
									});
									
									$("#weebotLite textarea").css("height","auto").val("").attr("disabled","disabled");
									$("#weebotLite .extended .buttons").addClass("hide");
									
									var send = false;
									if(text!="") {
										var send = true;
										if(options["spam"]==1) {
											if(text.length<options["spam:chars"]) {
												var send = false;
											}
											if(options["spam:words"]==1) {
												var words = text.split(" ");
												if(options["spam:words:include"]!="") {
													send = false;
													var words_include = options["spam:words:include"].split(",");
													for(var i=0;i<words_include.length;i++) {
														for(var j=0;j<words.length;j++) {
															if(words_include[i].replace(/ /gi,"")==words[j]) {
																send = true;
															}
														}
													}
												}
												if(options["spam:words:exclude"]!="") {
													var words_exclude = options["spam:words:exclude"].split(",");
													for(var i=0;i<words_exclude.length;i++) {
														for(var j=0;j<words.length;j++) {
															if(words_exclude[i].replace(/ /gi,"")==words[j]) {
																send = false;
															}
														}
													}
												}
												if(options["spam:strings:exclude"]!="") {
													var strings_exclude = options["spam:strings:exclude"].split(",");
													for(var i=0;i<strings_exclude.length;i++) {
														if(text.indexOf(strings_exclude[i].replace(/ /gi,""))>-1) {
															send = false;
														}
													}
												}
											}
										}
									}
									if(send===true) {
									
										var audio = $("#weebotLite audio.send");
										audio[0].play();
									
										$("#weebotLite .chat .content").append("<div class='question'><div class='message'>"+textHTML+"</div></div>"+loader);
										$("#weebotLite .chat").nanoScroller();
										$("#weebotLite .chat").nanoScroller({ scroll: 'bottom' });
										
										var page = window.location.href;
										if( options["page:parameter"]==1 ) {
											page = page.replace(window.location.search,"");
										} 
										if( options["page:hash"]==1 ) {
											page = page.replace(window.location.hash,"");
										}
										
										var parent = 0;
										if($("#weebotLite .chat .answer").last().length>0) {
											if(typeof($("#weebotLite .chat .answer").last().data("parent"))!=typeof(undefined)) {
												parent = $("#weebotLite .chat .answer").last().data("parent");
											}
										}
										$.ajax({
											type: "POST",
											url: weebotLiteAJAX.ajaxurl,
											data: {
												action: "weebotLiteAJAX",
												question: text,
												page: page,
												parent: parent,
												method: "chat:send"
											},
											xhrFields: {
												withCredentials: true
											},
											success: function(data){
												
												var audio = $("#weebotLite .income");
												audio[0].play();
											
												$("#weebotLite textarea").removeAttr("disabled");
												$("#weebotLite .extended .buttons").removeClass("hide");

												var dataArr = data.split('<!weebotLite!>');
												
												if($("#weebotLite_"+dataArr[0]+"_answer").length>0) {
													$("#weebotLite_"+dataArr[0]+"_answer").prev().remove();
													$("#weebotLite_"+dataArr[0]+"_answer").remove();
												}
												
												$(".weebotLite-loader").remove();
												
												$("#weebotLite .chat .content").append(dataArr[1]);
												
												$("#weebotLite .chat").nanoScroller();
												$("#weebotLite .chat").nanoScroller({ scroll: 'bottom' });
												
												weebotLite.email();
												
												if(weebotLite.checkSupportInterval===false) {
													weebotLite.checkSupportInterval = setInterval(weebotLite.checkSupport, 3000);
												}
												if(weebotLite.checkFallbackInterval===false) {
													weebotLite.checkFallbackInterval = setInterval(weebotLite.checkFallback, 30000);
												}
												
											}
										});
										
									} else {
										$("#weebotLite textarea").removeAttr("disabled");
										$("#weebotLite .extended .buttons").removeClass("hide");
										if(text!="") {
											$("#weebotLite .chat").append(
												$("<div />")
													.attr({class:"error"})
													.html($("#weebotLite .button.send").data("error"))
													.animate({opacity:0},4000,function() {
														$(this).remove();
													})
											);
										}
									}
								});
								
								/*
								 *	Track online user
								 */
								if( options["online"]==1 ) {
								
									var page = window.location.href;
								
									window.setInterval(function() {
										if( options["page:parameter"]==1 ) {
											page = page.replace(window.location.search,"");
										} 
										if( options["page:hash"]==1 ) {
											page = page.replace(window.location.hash,"");
										}
										
										$.ajax({
											type: "POST",
											url: weebotLiteAJAX.ajaxurl,
											data: {
												action: "weebotLiteAJAX",
												method: "online",
												page: page
											},
											xhrFields: {
												withCredentials: true
											}
										})
									}, 3000);
									
								}
								
								$(document).mousemove(function(event) {
									weebotLite.currentMousePos.x = event.pageX;
									weebotLite.currentMousePos.y = event.pageY;
								});
								
								var showChat = (options["chat:seconds"]!="") ? options["chat:seconds"]*1000 : 2000;
								
								window.setTimeout(function() {
									$("#weebotLite .small").addClass("visible");
								}, showChat);
								
							}
						});
						
					}
				}
			});
		}
	
		/*
		*	get the users email and set email-options
		*/
		this.settingsEmail = function() {
			$.ajax({
				type: "POST",
				url: weebotLiteAJAX.ajaxurl,
				data: {
					action: "weebotLiteAJAX",
					question: $("#weebotLite .settings").attr("data-question-id"),
					email: $("#weebotLite-user-email").val(),
					name: $("#weebotLite-user-name").val(),
					method: "email"
				},
				xhrFields: {
					withCredentials: true
				},
				success: function(data){
					var el = $("#weebotLite_"+$("#weebotLite .settings").attr("data-question-id")+"_answer .email");
					$(el).removeClass("email").find(".hidden").removeClass("hidden").addClass("block");
					$(el).find(".visible").addClass("hidden");
				}
			});
		}
		
		/*
		*	get answer via email
		*/
		this.email = function() {
			$("#weebotLite .email").unbind("click");
			$("#weebotLite .email").bind("click",function() {
				$("#weebotLite .settings").attr("data-question-id",$(this).data("question-id"));
				if($("#weebotLite-user-email").val()==""||weebotLite.checkEmail.test($("#weebotLite-user-email").val())===false) {
					$("#weebotLite .settings").removeClass("hidden");
				} else {
					weebotLite.settingsEmail();
				}
			});
		}
		
		/*
		*	check for new answers to supported questions
		*/
		this.checkSupport = function(type) {
			weebotLite.checkAnswers("support");
		}
		
		/*
		*	check for new answers to fallbacked questions
		*/
		this.checkFallback = function(type) {
			weebotLite.checkAnswers("fallback");
		}
		
		/*
		*	get answers
		*/
		this.checkAnswers = function(type) {
			if($("#weebotLite .unanswered.type-"+type).length>0) {
				var ids = new Array();
				$.each($("#weebotLite .unanswered.type-"+type),function() {
					if($(this).data("question-id")!="") {
						ids.push($(this).data("question-id"));
					}
				});
				if(ids.length>0) {
					$.ajax({
						type: "POST",
						url: weebotLiteAJAX.ajaxurl,
						data: {
							action: "weebotLiteAJAX",
							questions: ids,
							type: type,
							method: "check"
						},
						xhrFields: {
							withCredentials: true
						},
						success: function(data){
							var dataArr = data.split('<!weebotLite!>');
							for(var i=0;i<dataArr.length;i++) {
								if(typeof(dataArr[i+1])!=typeof(undefined)) {
									if(dataArr[i+1]=="1") {
										$("#weebotLite-callback-"+dataArr[i]).removeClass("hidden");
									} else {
										if(dataArr[i+1].length>1) {
											$("#weebotLite-callback-"+dataArr[i]).html(dataArr[i+1]).removeClass("hidden").parent().parent().removeClass("unanswered").find(".fallback").addClass("hidden");
											var audio = $("#weebotLite .income");
											audio[0].play();
										}
									}
								}
								i++;
							}
						}
					});
				}
			} else {
				switch(type) {
					case "fallback":
						clearInterval(weebotLite.checkFallbackInterval);
						weebotLite.checkFallbackInterval = false;
					break;
					case "support":
						clearInterval(weebotLite.checkSupportInterval);
						weebotLite.checkSupportInterval = false;
					break;
				}
			}
		}
	
	}

})( jQuery );
