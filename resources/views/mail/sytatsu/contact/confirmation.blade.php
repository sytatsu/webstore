@extends('mail.sytatsu.base')

@section('content')
    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: bold; color: #1C315E; text-transform: uppercase; margin-top: 16px; margin-bottom: 0;">Contact Confirmation</h1>
    </div>

    <p style="margin-bottom: 24px; font-size: 16px; color: #4b5563; text-align: center;">
        Thank you for getting in touch! We have received your message and will get back to you as soon as possible. Below is a copy of the information you provided.
    </p>

    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
        <div style="margin-bottom: 16px;">
            <strong style="display: block; font-size: 14px; color: #64748b; text-transform: uppercase;">Name</strong>
            <span style="font-size: 16px; color: #1e293b;">{{ $name }}</span>
        </div>
        <div style="margin-bottom: 16px;">
            <strong style="display: block; font-size: 14px; color: #64748b; text-transform: uppercase;">Email</strong>
            <span style="font-size: 16px; color: #1e293b;">{{ $email }}</span>
        </div>
        <div style="margin-bottom: 16px;">
            <strong style="display: block; font-size: 14px; color: #64748b; text-transform: uppercase;">Phone</strong>
            <span style="font-size: 16px; color: #1e293b;">{{ $phone ?? 'N/A' }}</span>
        </div>
        <div>
            <strong style="display: block; font-size: 14px; color: #64748b; text-transform: uppercase;">Message</strong>
            <div style="font-size: 16px; color: #1e293b; white-space: pre-wrap;">{{ $details }}</div>
        </div>
    </div>
@endsection
