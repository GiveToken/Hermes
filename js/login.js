function loginOpen() {
  loginClear();
  $("#login-dialog").modal();
}

function loginClear() {
  $('#login-alert-placeholder').html('<div></div>');
  $('#login_email').val("");
  $('#password').val("");
}

function loginClose() {
  $('#login-dialog').modal('hide');
}

function loginError(message) {
  $('#login-alert-placeholder').html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><span>'+message+'</span></div>');
}

function loginInfo(message) {
  $('#login-alert-placeholder').html('<div class="alert alert-info"><span>'+message+'</span></div>');
}

function loginSuccess(app_root) {
  $('#login-alert-placeholder').html('<div class="alert alert-success"><span>You have successfully logged into S!zzle.</span></div>');
  setTimeout(function(){
    $('#login-dialog').modal('hide');
    document.location.href = app_root;
  }, 500);
}

function loginFacebook() {
  loginInfo("Logging in with Facebook...");
  FB.login(function(response){
    handleFBLogin(response);
  }, {
    scope: 'user_photos, public_profile, email'
  });
}

function getQueryParameters(str) {
  // Taken from https://github.com/youbastard/jquery.getQueryParameters/blob/master/qp.js
  return (str || document.location.search).replace(/(^\?)/, '').split("&").map(function (n) {
    return n = n.split("="), this[n[0]] = n[1], this;
  }.bind({}))[0];
}

function processLogin(userInfo) {
  loginInfo("Logging into S!zzle.  Please wait...");
  $('.dialog-button-center').addClass("disable-clicks");
  $.post("/ajax/login", userInfo, function(data, textStatus, jqXHR){
    if(data.status === "SUCCESS") {
      var redirect = decodeURIComponent(getQueryParameters()['next']);
      if (redirect == 'undefined') {
        redirect = "admin";
      }
      if (redirect.startsWith('/')) {
        redirect = redirect.slice(1);
      }
      loginSuccess(data.app_root+redirect);
    } else if (data.status === "ERROR") {
      loginError("Login failed. "+data.message);
    }  else {
      loginError("Login failed. Unknown return status: "+data.status);
    }
  }).fail(function() {
    loginError("Login failed.");
  }).always(function() {
    $('.dialog-button-center').removeClass("disable-clicks");
  });
}

function loginEmail() {
  if (!$("#login_email").val()) {
    loginError("An email address is required when logging in with email.");
  } else if (!$("#password").val()) {
    loginError("A password is required when logging in with email.");
  } else {
    processLogin($("#login-form").serialize());
  }
}

function handleFBLogin(response) {
    if (response.status === 'connected') {
    FB.api('/me?fields=email,last_name,first_name', function(api_response) {
      api_response.login_type = "FACEBOOK";
      api_response.login_email = api_response.email;
      response.email = api_response.email;
      response.access_token = FB.getAuthResponse().accessToken;
      $('.dialog-button-center').addClass("disable-clicks");
      $.post("ajax/user/update_access_token", response, function(data, textStatus, jqXHR){
        if(data.status === "SUCCESS"){
          processLogin(api_response);
        } else if (data.status === "ERROR"){
          loginError("Facebook authorization failed on login.");
        } else if (data.status === "NO_USER"){
          loginError("This Facebook account is not registered. Please use the sign up button to continue.");
        }
      }).fail(function() {
        loginError("Facebook authorization failed");
      }).always(function() {
        $('.dialog-button-center').removeClass("disable-clicks");
      });
    });
    } else if (response.status === 'not_authorized') {
    // The person is logged into Facebook, but not your app.
    loginError("Log In with Facebook failed. Facebook response status: "+response.status);
    } else {
    // The person is not logged into Facebook, so we're not sure if they are logged into this app or not.
    loginError("Log In with Facebook failed. Facebook response status: "+response.status);
    }
}

/* For handeling pressing enter to Submit... why are the buttons not in the form to begin with? */
$('#login_email').keypress(function(e) {
if (e.which == '13') {
         loginEmail();
   }
});
$('#password').keypress(function(e) {
if (e.which == '13') {
         loginEmail();
   }
});
