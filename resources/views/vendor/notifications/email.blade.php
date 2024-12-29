<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
    <h1 style="font-family: 'Arial', sans-serif; color: #333; font-size: 24px; text-align: center; margin-top: 20px;">
        {{ $greeting }}
    </h1>
@else
    <h1 style="font-family: 'Arial', sans-serif; color: #333; font-size: 24px; text-align: center; margin-top: 20px;">
        @lang('Hello User!')
    </h1>
@endif

{{-- Intro Lines --}}
<p style="font-family: 'Arial', sans-serif; font-size: 16px; color: #555; line-height: 1.6; text-align: center; padding: 0 20px;">
    You are receiving this email because we received a password reset request for your account.
</p>

{{-- Company Logo --}}
<div style="text-align: center; margin-top: 30px;">
    <img src="{{ asset('images/Colormode.png') }}" alt="Company Logo" style="width: 200px; height: auto; border-radius: 8px;"/>
</div>

{{-- Action Button --}}
<x-mail::button :url="$actionUrl" :color="'red'" style="font-family: 'Arial', sans-serif; margin-top: 30px; background-color: #e63946; border-radius: 5px; color: #fff; padding: 15px 30px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; display: block; width: 50%; margin: 30px auto; text-align: center; text-decoration: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: background-color 0.3s ease-in-out;">
    Reset Password
</x-mail::button>

{{-- Outro Lines --}}
<p style="font-family: 'Arial', sans-serif; font-size: 16px; color: #555; line-height: 1.6; text-align: center;">
    If you did not request a password reset, no further action is required.
</p>

{{-- Salutation --}}
<p style="font-family: 'Arial', sans-serif; font-size: 16px; color: #555; text-align: center; margin-top: 20px;">Regards,</p>
<p style="font-family: 'Arial', sans-serif; font-size: 16px; color: #555; text-align: center;">Color Mode Nepal, Where Music meets Art</p>

{{-- Footer --}}
<div style="margin-top: 40px; text-align: center; font-size: 12px; color: #888; font-family: 'Arial', sans-serif; line-height: 1.6;">
    <p>You received this email because you signed up at our site. If you didn't request this, please ignore this email.</p>
    <p>All Rights Reserved <strong>Colo Mode Nepal</strong> &copy; 2024</p>
</div>

{{-- Subcopy --}}
<x-slot:subcopy>
    @lang(
        "If you're having trouble clicking the \"Reset Password\" button, copy and paste the URL below\n".
        'into your web browser: ',
    )
    <span class="break-all" style="font-family: 'Arial', sans-serif; color: #555; font-size: 14px;">
        [{{ $displayableActionUrl }}]({{ $actionUrl }})
    </span>
</x-slot:subcopy>

</x-mail::message>
