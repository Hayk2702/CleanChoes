<!DOCTYPE html>
<html>
<head>
    <title>New Contact Request</title>
</head>
<body>
<h2>New Contact Submission</h2>

<p><strong>Name:</strong> {{ $data['name'] }}</p>

@if(!empty($data['email']))
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
@endif

<p><strong>Phone:</strong> {{ $data['phone'] }}</p>

@if(!empty($data['comment']))
    <p><strong>Comment:</strong> {{ $data['comment'] }}</p>
@endif

</body>
</html>
