<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<table style="width: 100%!important; height: 100%; background-color: #f8fafc; padding: 20px; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, 'Lucida Grande', sans-serif; font-size: 100%; line-height: 1.6;">
    <tr>
        <td></td>
        <td style="background-color: #ffffff; border-radius:8px; display:block!important; max-width:600px!important; margin:0 auto!important; clear:both!important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
            <div style="padding:20px; max-width:600px; margin:0 auto; display:block;">
                <table style="width: 100%;">
                    <tr>
                        <td>
                            <p style="text-align: center; display: block; padding-bottom:20px; margin-bottom:20px; border-bottom:1px solid #e2e8f0;">
                                <img style="padding: 0 20px" width="240px" src="{{ $message->embed(public_path('images/brands/no_background_text_only.png')) }}"/>
                            </p>

                            @yield('content')

                            <p style="text-align: center; display: block; padding-top:20px; font-weight: bold; margin-top:30px; border-top:1px solid #e2e8f0;">
                                <a href="https://sytatsu.nl/" style="color: #E14C04; text-decoration: none;">Sytatsu.nl</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
</table>
</body>
</html>
