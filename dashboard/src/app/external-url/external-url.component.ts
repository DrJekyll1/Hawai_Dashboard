import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-external-url',
  templateUrl: './external-url.component.html',
  styleUrls: ['./external-url.component.css']
})
export class ExternalUrlComponent implements OnInit {

  constructor() { }

  ngOnInit() {
    window.location.href = 'http://localhost:5000/';
  }

}
