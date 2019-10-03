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
    },
    getUserPaidInvoiceList: function (val)
    {
        if ($.trim(val) != "") {
            $(".global-loader").show();
            $.ajax({
                type: "GET",
                url: baseUrl + 'payment-received/get-paid-invoice',
                data: {
                    'id':val
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
    showPaidSection:function()
    {
        var is_paid = $("#monthlyinvoice-is_paid").is(":checked");
        if(is_paid){
            $("#paid-section").show();
        }else{
            $("#paid-section").hide();
        }
    }
};

