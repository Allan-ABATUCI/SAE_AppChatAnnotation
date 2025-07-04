(function ($) {
  "use strict";

  /*==================================================================
    [ Focus input ]*/
  $(".input100").each(function () {
    $(this).on("blur", function () {
      if ($(this).val().trim() != "") {
        $(this).addClass("has-val");
      } else {
        $(this).removeClass("has-val");
      }
    });
  });

  /*==================================================================
    [ Validate ]*/
  var input = $(".validate-input .input100");

  $(".validate-form").on("submit", function () {
    var check = true;

    for (var i = 0; i < input.length; i++) {
      if (validate(input[i]) == false) {
        showValidate(input[i]);
        check = false;
      }
    }

    return check;
  });

  $(".validate-form .input100").each(function () {
    $(this).focus(function () {
      hideValidate(this);
    });
  });

  function validate(input) {
    if ($(input).attr("type") == "email" || $(input).attr("name") == "email") {
      if (
        $(input)
          .val()
          .trim()
          .match(
            /^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/
          ) == null
      ) {
        return false;
      }
    } else {
      if ($(input).val().trim() == "") {
        return false;
      }
    }
  }

  function showValidate(input) {
    var thisAlert = $(input).parent();

    $(thisAlert).addClass("alert-validate");
  }

  function hideValidate(input) {
    var thisAlert = $(input).parent();

    $(thisAlert).removeClass("alert-validate");
  }
})(jQuery);




function postToChat(userId) {
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "/?controller=chat";

  const input = document.createElement("input");
  input.type = "hidden";
  input.name = "user_id";
  input.value = userId;
  form.appendChild(input);

  document.body.appendChild(form);
  form.submit();
}
// === Gestion des réactions emoji ===
document.querySelectorAll('.emoji-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const emoji = btn.dataset.emoji;
    const messageDiv = btn.closest('.message');
    const messageId = messageDiv.dataset.id;

    const res = await fetch('index.php?controller=chat&action=react', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ messageId, emoji })
    });

    const json = await res.json();
    if (json.status === 'success') {
      document.getElementById('reaction-' + messageId).textContent = emoji;
    } else {
      alert('Erreur : ' + json.message);
    }
  });
});
