import $ from "jquery";
import "what-input";

// Foundation JS relies on a global varaible. In ES6, all imports are hoisted
// to the top of the file so if we used`import` to import Foundation,
// it would execute earlier than we have assigned the global variable.
// This is why we have to use CommonJS require() here since it doesn't
// have the hoisting behavior.
window.jQuery = $;
require("foundation-sites");

// If you want to pick and choose which modules to include, comment out the above and uncomment
// the line below
//import './lib/foundation-explicit-pieces';

$(document).foundation();

/*** CONTACT FORM ******/
$("#contactform").on("submit", function(ev, frm) {
  ev.preventDefault();
  //alert("elcsipve");

  //get input field values
  var user_name = $("input[name=name]").val();
  var user_email = $("input[name=email]").val();
  var user_tel = $("input[name=tel]").val();
  var user_msg = $("textarea[name=text]").val();

  var proceed = true;
  if (user_name === "") {
    //$('input[name=message_name]').css('border-color', '#e41919');
    proceed = false;
  }
  if (user_email === "") {
    //$('input[name=message_email]').css('border-color', '#e41919');
    proceed = false;
  }

  if (user_tel === "") {
    //$('input[name=message_tel]').css('border-color', '#e41919');
    proceed = false;
  }

  if ($("input:checkbox[name=acceptgdpr]:checked").length < 1) {
    proceed = false;
  }

  //everything looks good! proceed...
  if (proceed) {
    //alert(konzarray);
    //alert(timearray);
    //data to be sent to server
    var post_data = {
      userName: user_name,
      userEmail: user_email,
      userTel: user_tel,
      userMsg: user_msg,
    };
    $("#contact_submit").addClass("disabled");
    $("#contact_submit").attr("disabled", "disabled");
    $("#contact_submit").text("Küldés folyamatban");

    //Ajax post data to server
    $.post(
      $("#contactform").attr("action"),
      post_data,
      function(response) {
        var output = "";

        //load json data from server and output message
        if (response.type === "error") {
          output = '<p class="error">' + response.text + "</p>";
        } else {
          output = '<p class="success">' + response.text + "</p>";

          //reset values in all input fields
          $("#contactform input").val("");
          $("#contactform textarea").val("");
        }
        $("#result")
          .hide()
          .html(output)
          .slideDown();
        $("#contact_submit").removeClass("disabled");
        $("#contact_submit").removeAttr("disabled");
        $("#contact_submit").text("Visszahívást kérek!");
      },
      "json"
    );
  }

  return false;
});

//reset previously set border colors and hide all message on .keyup()
$("#contactform input, #contactform textarea, #contactform #accept").keyup(function() {
  //$("#contactform input, #contactform textarea").css('border-color', '');
  $("#result").slideUp();
  $("#formerror").slideUp();
});

$("#contactform #accept").on("change", function() {
  $("#result").slideUp();
  $("#formerror").slideUp();
});
