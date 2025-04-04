<?= $head ?>

<div class="wrapper">

    <div class="banner">
    </div>
    <div class="content">
        <form method="post" id="send-message">
            <div class="form-group row mb-3">
                <label class="col-sm-3 form-label" for="message">Message</label>
                <div class="col-sm-9">
                    <input class=" form-control" type="text" id="message" name="message"></label>
                </div>
            </div>
            <div class="form-group row">
                <button class="btn btn-primary">Send message</button>
            </div>
        </form>
    </div>



</div>
<?= $footer ?>


<script>
    $(document).ready(function () {
        $('#send-message').submit(function (e) {
            e.preventDefault();
            var message = $('#message').val();
            $.ajax({
                url: '<?= $send_message ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    message: message
                }
            }).done(function (data) {
                var responderInput = $('<input>')
                    .addClass('form-control mt-3')
                    .attr('type', 'text')
                    .attr('readonly', true)
                    .val(data.responder_link)
                    .on('click', function () {
                        this.select();
                    });

                var hostInput = $('<input>')
                    .addClass('form-control mt-2')
                    .attr('type', 'text')
                    .attr('readonly', true)
                    .val(data.host_link)
                    .on('click', function () {
                        this.select();
                    });

                $('.content').append('<label class="mt-3">Responder Link</label>', responderInput);
                $('.content').append('<label class="mt-2">Host Link</label>', hostInput);
            });

        });
    });
</script>