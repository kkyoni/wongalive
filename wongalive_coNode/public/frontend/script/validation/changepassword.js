$("#changePasswordFrm").validate({
  	rules: {
    	new_password: {
          required: true,
          minlength: 5
      },
      confirm_password: {
          required: true,
          equalTo : "#new_password",
          minlength: 5
      }
  	},
    messages: {
      new_password: {
        required: "Please enter new password",
        minlength: "Your password must be at least 5 characters long"
      },
      confirm_password: {
          required: "Please enter confirm password",
          equalTo: "Confirm password not matched with new password",
          minlength: "Your password must be at least 5 characters long"
      }
    },
    submitHandler: function (form) {
        form.submit();
    }
});