$((function(){var e=function(e){$(".contact-error-message").html(e).show()},t=function(t){var s="";$.each(t,(function(e,t){""!==s&&(s+="<br />"),s+=t})),e(s)};$(document).on("click",".contact-form button[type=submit]",(function(s){var o=this;s.preventDefault(),s.stopPropagation(),$(this).addClass("button-loading"),$(".contact-success-message").html("").hide(),$(".contact-error-message").html("").hide(),$.ajax({type:"POST",cache:!1,url:$(this).closest("form").prop("action"),data:new FormData($(this).closest("form")[0]),contentType:!1,processData:!1,success:function(t){var s;t.error?e(t.message):($(o).closest("form").find("input[type=text]").val(""),$(o).closest("form").find("input[type=email]").val(""),$(o).closest("form").find("textarea").val(""),s=t.message,$(".contact-success-message").html(s).show()),"undefined"!=typeof refreshRecaptcha&&refreshRecaptcha()},error:function(s){var o;"undefined"!=typeof refreshRecaptcha&&refreshRecaptcha(),void 0!==(o=s).errors&&o.errors.length?t(o.errors):void 0!==o.responseJSON?void 0!==o.responseJSON.errors?422===o.status&&t(o.responseJSON.errors):void 0!==o.responseJSON.message?e(o.responseJSON.message):$.each(o.responseJSON,(function(t,s){$.each(s,(function(t,s){e(s)}))})):e(o.statusText)},complete:function(){return $(o).removeClass("button-loading")}})}))}));