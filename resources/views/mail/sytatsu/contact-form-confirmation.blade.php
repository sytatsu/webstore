<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<table bgcolor="#fffaf7" style=" width: 100%!important; height: 100%; background-color: #fffaf7; padding: 20px; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, 'Lucida Grande', sans-serif;  font-size: 100%; line-height: 1.6;">
    <tr>
        <td></td>
        <td bgcolor="#ffffff" style="border: 1px solid #eeeeee; background-color: #ffffff; border-radius:5px; display:block!important; max-width:600px!important; margin:0 auto!important; clear:both!important;"><div style="padding:20px; max-width:600px; margin:0 auto; display:block;">
                <table style="width: 100%;">
                    <tr>
                        <td><p style="text-align: center; display: block;  padding-bottom:20px;  margin-bottom:20px; border-bottom:1px solid #dddddd;"><img style="padding: 0 20px" width="520px" src="{{ $message->embed(public_path('images/brands/brand.png')) }}"/></p>
                            <p style="margin-bottom: 10px; font-weight: normal; font-size:16px; color: #333333;">Thank you for getting in contact! Underneath you will find a copy of all information you have filled in.</p>
                            <h2 style="font-weight: 200; font-size: 16px; margin: 20px 0; color: #333333;"><b>Name:</b> {{ $name }} </h2>
                            <h2 style="font-weight: 200; font-size: 16px; margin: 20px 0; color: #333333;"><b>Email:</b> {{ $email }} </h2>
                            <h2 style="font-weight: 200; font-size: 16px; margin: 20px 0; color: #333333;"><b>Phone:</b> {{ $phone }} </h2>
                            <h2 style="font-weight: 200; font-size: 16px; margin: 20px 0; color: #333333;"><b>Message:</b> <br/> {{ $details }} </h2>
                            <a href="{{ route('sytatsu.welcome') }}" style="text-align: center; display: block; padding-top:20px; font-weight: bold; margin-top:30px; color: #7ad1f1; border-top:1px solid #dddddd;">Sytatsu.nl</a></td>
                    </tr>
                </table>
            </div></td>
        <td></td>
    </tr>
</table>
</body>
</html>
