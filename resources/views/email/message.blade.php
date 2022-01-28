<html>
    <body>

        <p>Dear {!! $name !!},</p>
        <br>
        <p>Please click the link below to activate your account:</p>
        <a href="{!! url('/') !!}/activate/account/{!! $id !!}" target="_blank">Activate</a>
        <br>
        <br>
        <p>ExecomIS System</p>

    </body>
</html>

