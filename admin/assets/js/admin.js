(function( $ ) {
	'use strict';
	
	/**
	 * Admin javascript for the support-chat
	 *
	 * @link       http://www.weedesign.de/weebotLite.html
	 * @since      1.0.0
	 *
	 * @package    weebotLite
	 * @subpackage weebotLite/admin/assets
	 */
	 
	
	/*
	*	run the support chat
	*/
	$( window ).load(function() {
		weebotLiteAdmin.run();
	});
	
	var weebotLiteAdmin = new function() {
		
		/*
		*	lookup existing answers
		*/
		this.lookup = function(type,id) {
			var query = $("#weebotLite-lookup-"+type).val();
			if(query!="") {
				var loader = '<div id="weebotLite-loader" class="weebotLite-loader">';
				for(var i=1;i<9;i++) {
					loader+= '<div class="loader" id="weebotLite-loader-'+i+'"></div>';
				}
				loader+= '</div>';
				$("#weebotLite-lookup-"+type+"-results").html(loader);
				$.ajax({
					type: "POST",
					url: weebotLiteAJAXadmin.ajaxurl,
					data: {
						action: "weebotLiteAJAXadmin",
						method: "lookup",
						data: {
							type: type,
							query: query,
							id: id
						}
					},
					success: function(data){
						$("#weebotLite-lookup-"+type+"-results").html(data).addClass("visible");
						var options = {
						  valueNames: [ 'type', 'message' ]
						};
						var list = new List('weebotLite-lookup-'+type+'-list', options);
						$(".use-existing-answer").bind("click",function() {
							$("#weebotLite-form-"+$(this).data("form")+" input.existing").val($(this).data("id"));
							$("#weebotLite-form-"+$(this).data("form")+" #submit").trigger("click");
						});
					}
				});
			} else {
				$("#weebotLite-lookup-"+type+"-results").html($("#weebotLite-lookup-"+type+"-submit").data("error")).addClass("visible");
			}
		}
		
		/*
		*	check for new questions
		*/
		this.checkNewQuestions = function() {
			$.ajax({
				type: "POST",
				url: weebotLiteAJAXadmin.ajaxurl,
				data: {
					action: "weebotLiteAJAXadmin",
					method: "check"
				},
				success: function(data){
					var dataArr = data.split('<!weebotLite!>');
					if($("#weebotLite_newQuestion_count").length==0) {
						$(document.body).append($('<input />').attr({ id : "weebotLite_newQuestion_count", type: "hidden", value: dataArr[0] }));
					} else {
						if($("#weebotLite_newQuestion_count").val()*1<dataArr[0]*1) {
							$("#wp-admin-bar-weebotLite .count").html(dataArr[0]);
						}
						$("#weebotLite_newQuestion_count").val(dataArr[0]);
					}
					if(dataArr[1]!="") {
						if($("#weebotLiteChat").length==0) {
							$(document.body).append($("<div />").attr({ id : "weebotLiteChat" }).addClass("nano").append($("<div />").addClass("content nano-content")));
						}
						
						$('#weebotLiteChat .content').append(dataArr[1]);
						
						$("#weebotLiteChat").nanoScroller();
						$("#weebotLiteChat").nanoScroller({ scroll: 'bottom' });
						
						$('#weebotLiteChat .answer.button').unbind("click");
						$('#weebotLiteChat .answer.button').bind("click",function() {
							$("#answer_question_"+$(this).data("question-id")).removeClass("hidden");
							$("#weebotLiteChat").nanoScroller();
						});
						$('#weebotLiteChat .close').unbind("click");
						$('#weebotLiteChat .close').bind("click",function() {
							$(this).parent().remove();
							$("#weebotLiteChat").nanoScroller();
							if($('#weebotLiteChat .close').length==0) {
								$("#weebotLiteChat").remove();
							}
						});
						$('#weebotLiteChat form').unbind("submit");
						$('#weebotLiteChat form').bind("submit",function() {
							var page = ($(this).find("input.page").is(":checked")) ? $(this).find("input.page").val() : 0;
							var form = this;
							$.ajax({
								type: "POST",
								url: weebotLiteAJAXadmin.ajaxurl,
								data: {
									action: "weebotLiteAJAXadmin",
									method: "answer",
									data: {
										user: $(this).find("input.user-id").val(),
										question: $(this).find("input.question-id").val(),
										type: $(this).find("select.type").val(),
										action: $(this).find("input.action").val(),
										subject: $(this).find("input.subject").val(),
										file: $(this).find("select.files").val(),
										page: page
									}
								},
								success: function(data){
									$(form).parent().parent().remove();
									if($("#weebotLiteChat .question").length==0) {
										$("#weebotLiteChat").remove();
									}
									$("#weebotLiteChat").nanoScroller();
								}
							});
							return false;
						});
						weebotLiteAdmin.formSelectFiles();
					}
				}
			});
		}
		
		/*
		*	run the support chat
		*/
		this.run = function() {
			$('.weebotLite_USER_select').bind("change",function() {
				var select = this;
				$.ajax({
					type: "POST",
					url: weebotLiteAJAXadmin.ajaxurl,
					data: {
						action: "weebotLiteAJAXadmin",
						user: $(this).data("user"),
						type: $(this).find("option:selected").val(),
						method: "user"
					},
					success: function(data){
						$(select).after('<span id="weebotLiteInfo">'+$(select).data("success")+'</span>');
						$("#weebotLiteInfo").animate({opacity:0},3000,function() {
							$(this).remove();
						});
					}
				});
			});
			$("#questionsForm,#eventsForm,#userForm").bind("submit",function() {
				if($("#post-search-input").val()!="") {
					var action = $("#questionsForm,#eventsForm,#userForm").attr("action");
					action = action.split("s=");
					$("#questionsForm,#eventsForm,#userForm").attr("action",action);
				}
			});
			$(".weebotLite-delete").bind("click",function(e) {
				e.preventDefault();
				var element = (typeof($(this).data("remove"))!=typeof(undefined)) ? "#"+$(this).data("remove") : "#post-"+$(this).data("id");
				$.ajax({
					type: "POST",
					url: weebotLiteAJAXadmin.ajaxurl,
					data: {
						action: "weebotLiteAJAXadmin",
						id: $(this).data("id"),
						table: $(this).data("table"),
						method: "delete"
					},
					success: function(data){
						$(element).animate({opacity:0,height:0},500,function() {
							$(element).remove();
						});
					}
				});
			});
			
			$.ajax({
				type: "POST",
				url: weebotLiteAJAXadmin.ajaxurl,
				data: {
					action: "weebotLiteAJAXadmin",
					method: "init"
				},
				success: function(data){
					if(data!="") {
						setInterval(weebotLiteAdmin.checkNewQuestions, 3000);
					}
				}
			});
		
			

			weebotLiteAdmin.formSelectFiles();
			
			if($("#weebotLite-lookup-answers").length>0) {
				$("#weebotLite-lookup-answers").bind("keypress",function(e) {
					if(e.keyCode==13) {
						weebotLiteAdmin.lookup("answers",$(this).data("id"));
						return false;
					}
				});
				$("#weebotLite-lookup-answers-submit").bind("click",function() {
					weebotLiteAdmin.lookup("answers",$(this).data("id"));
				});
			}
			
			if($(".select2").length>0) {
				$(".select2").select2();
			}
		}
	
	}

})( jQuery );