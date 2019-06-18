import { Component, OnInit } from '@angular/core';
import { Observable } from 'rxjs/Observable';
import {AuthService} from '../../../services/auth.services';

@Component({
  selector: 'layout-footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.css']
})
export class FooterComponent implements OnInit {

  signedIn: Observable<boolean>;

  constructor(private authService: AuthService) {
    this.signedIn = authService.isSignedIn();
  }



  ngOnInit() {
  }

}
