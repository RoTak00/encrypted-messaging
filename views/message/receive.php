<?= $head ?>

<div class="wrapper">

    <div class="banner">
    </div>
    <div class="content">
        <div class="mb-4">
            <p><strong>Received on <?= $date ?>:</strong></p>
            <div class="alert alert-secondary"><?= htmlspecialchars($message) ?></div>
        </div>

        <form method="post" id="send-response">
            <div class="form-group row mb-3">
                <label class="col-sm-3 form-label" for="response">Your Response</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text" id="response" name="response">
                </div>
                <div id="form-error" class="text-danger">

                </div>
            </div>
            <div class="form-group row">
                <button class="btn btn-primary" id="btn-send-response">Send response</button>
            </div>
        </form>
        <div id="response-result" class="mt-3"></div>

    </div>



</div>
<?= $footer ?>
<script>
    $(document).ready(function () {
        $('#send-response').submit(function (e) {
            e.preventDefault();
            var response = $('#response').val();
            $.ajax({
                url: '<?= $send_response ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    response: response,
                }
            }).done(function () {
                $('#response-result').text('Response sent successfully.').addClass('text-success');
                $("#response").val('');
            }).fail(function (jqXHR) {
                var jsonResponse = jqXHR.responseJSON;

                if (jsonResponse.error) {
                    $("#form-error").text(jsonResponse.error);
                }
            });
        });

        $("#response").on("input", onFieldInput);
        onFieldInput();
    });


    function onFieldInput() {
        $("#form-error").text("");

        console.log($("#response").val().length);

        if ($("#response").val().length > 0) {
            $("#btn-send-response").removeAttr("disabled", false);
        }
        else {
            //$("#btn-send-response").attr("disabled", "disabled");
        }
    }
</script>