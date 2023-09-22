$("#registerFrm").validate({
  	rules: {
    	full_name: "required",
    	email_id: {
      		required: true,
      		email: true
    	},
      password: {
        required: true,
        minlength: 5        
      },
      confirm_password: {
        required: true,
        minlength: 5,
        equalTo: "#password"
      },
      terms_condition: "required"
  	},
    messages: {
      full_name: "Please enter your full name",
      email: {
        required: "Please enter your email address",
        email: "Please enter a valid email address",
      },
      password: {
        required: "Please enter a password",
        minlength: "Your password must be at least 5 characters long"
      },
      confirm_password: {
        required: "Please enter a password confirm",
        minlength: "Your password must be at least 5 characters long",
        equalTo: "Please enter the same password as above"
      },
      terms_condition: "Please select Tems & Condition",
    },
    submitHandler: function (form) {
        form.submit();
    }
});