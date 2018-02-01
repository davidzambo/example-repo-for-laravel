/*
  Hide the error feedback message at the transfer status block
*/
$('.invalid-feedback-transfer-status').hide();

$('input[name="transfer_status"]').on('change', function(){
  $('.invalid-feedback-transfer-status').hide();  
});

$(document).ready(() => {
  if ($('input[name="position[]"]:checked').length > 0)
    $('#position_check').prop({ 'disabled': true, 'required': false });
  else
    $('#position_check').prop({ 'disabled': false, 'required': true });  
});


$('input[name="position[]"]').on('change', () => {
  if ($('input[name="position[]"]:checked').length > 0)
    $('#position_check').prop({ 'disabled': true, 'required': false });
  else
    $('#position_check').prop({ 'disabled': false, 'required': true });
});

/*
  Registration form validation for update user details module
*/
(function () {
  'use strict';

  window.addEventListener('load', function () {
    var form = document.getElementById('updateUserDetails');

    form.addEventListener('submit', function (event) {
      if (form.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
        $('html, body').animate({
          scrollTop: ($('.form-control:invalid').offset().top - 300)
        }, 500);
        // console.log($('.form-control:invalid'));
      }
      if ($('input[name="transfer_status"]:checked').length === 0)
        $('.invalid-feedback-transfer-status').show();
      form.classList.add('was-validated');
    }, false);
  }, false);
})();