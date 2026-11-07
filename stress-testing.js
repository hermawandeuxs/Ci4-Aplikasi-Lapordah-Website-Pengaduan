import http from 'k6/http';
import { sleep } from 'k6';

export let options = {
  stages: [
    { duration: '30s', target: 10 },
    { duration: '30s', target: 30 },
    { duration: '30s', target: 50 },
    { duration: '30s', target: 0 },
  ],
};

export default function () {
  http.get('http://web-server.korat-ray.ts.net/pengaduan/reports');
  sleep(1);
}
