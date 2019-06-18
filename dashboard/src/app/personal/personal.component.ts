import { Component, OnInit } from '@angular/core';
import { OAuthService } from 'angular-oauth2-oidc';
import {User} from '../models/user';
import {AuthService} from '../services/auth.services';
import {Router} from '@angular/router';

@Component({
  selector: 'app-forbidden',
  templateUrl: './personal.component.html',
  styleUrls: ['./personal.component.css']
})
export class PersonalComponent implements OnInit {

  user: User;

  constructor(private authService: AuthService,
              private router: Router) { }

  ngOnInit() {
    this.user = this.authService.getUser();
  }

  /**
   * redirect to home component
   */
  home(): void {
    this.router.navigate(['home']);
  }


}
