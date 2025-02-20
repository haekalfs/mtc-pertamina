
(function ($) {
    "use strict";


    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit',function(){
        var check = true;

        for(var i=0; i<input.length; i++) {
            if(validate(input[i]) == false){
                showValidate(input[i]);
                check=false;
            }
        }

        return check;
    });


    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

    function validate (input) {
        if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        }
        else {
            if($(input).val().trim() == ''){
                return false;
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }



})(jQuery);

document.addEventListener("DOMContentLoaded", function () {
    var otpInputs = document.querySelectorAll(".otp-input");

    function setupOtpInputListeners(inputs) {
      inputs.forEach(function (input, index) {
        input.addEventListener("paste", function (ev) {
          var clip = ev.clipboardData.getData("text").trim();
          if (!/^\d{6}$/.test(clip)) {
            ev.preventDefault();
            return;
          }

          var characters = clip.split("");
          inputs.forEach(function (otpInput, i) {
            otpInput.value = characters[i] || "";
          });

          enableNextBox(inputs[0], 0);
          inputs[5].removeAttribute("disabled");
          inputs[5].focus();
          updateOTPValue(inputs);
        });

        input.addEventListener("input", function () {
          var currentIndex = Array.from(inputs).indexOf(this);
          var inputValue = this.value.trim();

          if (!/^\d$/.test(inputValue)) {
            this.value = "";
            return;
          }

          if (inputValue && currentIndex < 5) {
            inputs[currentIndex + 1].removeAttribute("disabled");
            inputs[currentIndex + 1].focus();
          }

          if (currentIndex === 4 && inputValue) {
            inputs[5].removeAttribute("disabled");
            inputs[5].focus();
          }

          updateOTPValue(inputs);
        });

        input.addEventListener("keydown", function (ev) {
          var currentIndex = Array.from(inputs).indexOf(this);

          if (!this.value && ev.key === "Backspace" && currentIndex > 0) {
            inputs[currentIndex - 1].focus();
          }
        });
      });
    }

    function updateOTPValue(inputs) {
      var otpValue = "";
      inputs.forEach(function (input) {
        otpValue += input.value;
      });

      if (inputs === otpInputs) {
        document.getElementById("verificationCode").value = otpValue;
      }
    }

    // Setup listeners for OTP inputs
    setupOtpInputListeners(otpInputs);

    // Initial focus on first OTP input field
    otpInputs[0].focus();
  });
