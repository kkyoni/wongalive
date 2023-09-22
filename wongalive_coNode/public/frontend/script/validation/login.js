$("#loginFrm").validate({
  	rules: {
    	login_email_id: {
      		required: true,
      		email: true
    	},
      login_password:  "required"
  	},
    messages: {
      login_email_id: {
        required: "Please enter your email address",
        email: "Please enter a valid email address",
      },
      login_password:  "Please enter a password"
    },
    submitHandler: function (form) {
        form.submit();
    }
});