$("#profileFrm").validate({
  	rules: {
      	key: "required",
      	secret: " required",
          merchantId : "required"
  	},
    messages: {
        key: "Please enter coinpayment key",
        secret: "Please enter coinpayment secret"
        merchantId : "Please enter coinpayment merchant id"
    },
    submitHandler: function (form) {
        form.submit();
    }
});