/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var app = {
  changeStatus: function (url, trigger, id, force_reload)
  {
    var status = 0;
    if ($('#' + trigger.id).is(":checked")) {
      status = 1;
    }
    $('.global-loader').show();
    $.ajax({
      type: "GET",
      url: baseUrl + url,
      data: {
        "id": id
      },
      success: function (res) {
        $(".global-loader").hide();
        if (force_reload)
        {
          location.reload();
        }
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        $(".global-loader").hide();
        console.log(jqXHR.responseText);
      }
    });
  },
  showHideMonthlyInvoice: function ()
  {
    if ($("#paymentreceived-has_invoice").is(":checked")) {
      $("#monthly-invoice").removeClass("hidden");
      $("#instalment-month-year").addClass("hidden");
      $("#paymentreceived-amount").val("0");
    } else {
      $("#monthly-invoice").addClass("hidden");
      $("#instalment-month-year").removeClass("hidden");
      $("#paymentreceived-amount").val("");
    }
  },
  addFundStatus: function () {
    var status = $("#status_id").val();
    var comments = $("#comments").val();
    if ($.trim(status) != "" && $.trim(comments) != "") {
      $(".global-loader").show();
      $.ajax({
        type: "POST",
        url: baseUrl + 'fund-request/add-status',
        data: $("#fund-request-status-form").serialize(),
        success: function (response)
        {
          $(".global-loader").hide();
          var result = JSON.parse(response);
          if (result.status == 201) {
            $("#response").html('<div class="alert alert-danger">' + result.msg + '</div>');
          }
          if (result.status == 200) {
            $("#response").html('<div class="alert alert-success">' + result.msg + '</div>');
            $.pjax.reload({container: '#fund-status-pjax'});
          }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          $(".global-loader").hide();
          alert(jqXHR.responseText);
        }
      })
    } else {
      $("#response").html('<div class="alert alert-danger">Select status and Enter your comments</div>');
      setTimeout(function () {
        $("#response").html("");
      }, 4000)
    }
  },
  getUserPaidInvoiceList: function (val)
  {
    if ($.trim(val) != "") {
      $(".global-loader").show();
      $.ajax({
        type: "GET",
        url: baseUrl + 'payment-received/get-paid-invoice',
        data: {
          'id': val
        },
        success: function (response)
        {
          $(".global-loader").hide();
          $("#paymentreceived-monthly_invoice_id").html(response);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          $(".global-loader").hide();
          alert(jqXHR.responseText);
        }
      })
    }
  },
  showPaidSection: function ()
  {
    var is_paid = $("#monthlyinvoice-is_paid").is(":checked");
    if (is_paid) {
      $("#paid-section").show();
    } else {
      $("#paid-section").hide();
    }
  },
  handleRadio: function () {
    if ($("input:radio[name='SendMailForm[sent_to]']").is(":checked")) {
      var sent_to = $('input[name="SendMailForm[sent_to]"]:checked').val();
      //console.log(sent_to);
      if (sent_to === 'S') {
        //$('#sendmailform-userids').removeAttr("disabled");
        app.addvalidation('w0', 'sendmailform-userids', 'SendMailForm[userIds][]', '.field-sendmailform-userids', 'Please select the value.')
      } else {
        app.removeValidation('w0', 'sendmailform-userids', '.field-sendmailform-userids')
        $('#sendmailform-userids').val('').trigger("change");
        //$('#sendmailform-userids').attr("disabled", "disabled");
      }
    }
  },
  addvalidation: function (form_id, id, name, container, errorMessage) {
    $(container).addClass("required");
    jQuery('#' + form_id).yiiActiveForm("add", {
      "id": id,
      "name": name,
      "container": container,
      "input": '#' + id,
      "validate": function (attribute, value, messages, deferred) {
        yii.validation.required(value, messages, {"message": errorMessage});
      }
    });
  },
  removeValidation: function (form_id, field_id, field_class) {
    $('#' + form_id).yiiActiveForm('remove', field_id);
    $(field_class).removeClass('has-error');
    $(field_class).removeClass("required");
    $(field_class).addClass('has-success');
    $(field_class + " .help-block").html('');
  },
};

