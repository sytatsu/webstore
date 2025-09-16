@extends('mail.sytatsu.base')

@section('content')
    <p style="margin-bottom: 10px; font-weight: normal; font-size:16px; color: #333333;">Thank you for getting in contact! Underneath you will find a copy of all information you have filled in.</p>
    <h2 style="font-weight: 200; font-size: 16px; margin: 20px 0; color: #333333;"><b>Name:</b> {{ $name }} </h2>
    <h2 style="font-weight: 200; font-size: 16px; margin: 20px 0; color: #333333;"><b>Email:</b> {{ $email }} </h2>
    <h2 style="font-weight: 200; font-size: 16px; margin: 20px 0; color: #333333;"><b>Phone:</b> {{ $phone }} </h2>
    <h2 style="font-weight: 200; font-size: 16px; margin: 20px 0; color: #333333;"><b>Message:</b> <br/> {{ $details }} </h2>
@endsection
