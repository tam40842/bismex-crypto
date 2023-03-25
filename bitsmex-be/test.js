import http from 'k6/http';
export default function () {
  var url = 'http://localhost:8000/api/trading/placedcd ';
  var payload = JSON.stringify({
    email: 'aaa',
    password: 'bbb',
  });
  var params = {
    headers: {
      'Content-Type': 'application/json',
        'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYwNjM2NzExMCwibmJmIjoxNjA2MzY3MTEwLCJqdGkiOiJHNGhUWDFCbkRwSWVHeEFzIiwic3ViIjoxLCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0.gM4R1K61_LSlVntxCpcVmEB-2M2gM4ryS20e0tGEfgM'
    },
  };
  http.post(url, payload, params);
}

//test request k6
//run: k6 run --vus 10 --duration 30s test.js