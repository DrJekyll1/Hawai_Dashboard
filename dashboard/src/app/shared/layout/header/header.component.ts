import { Component, OnInit } from '@angular/core';
import { OAuthService } from 'angular-oauth2-oidc';
import { Observable } from 'rxjs/Observable';
import {AuthService} from '../../../services/auth.services';
import { Router } from '@angular/router';
import { User } from '../../../models/user';

@Component({
  selector: 'layout-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

  signedIn: Observable<boolean>;
  user: User;


  constructor(private oauthService: OAuthService,
              private router: Router,
  private authService: AuthService) {
    this.user = this.authService.getUser();
    this.signedIn = authService.isSignedIn();
  }

  ngOnInit() {


  }
  get name() {
    const claims = this.oauthService.getIdentityClaims();
    if (!claims) {
      return null;
    }
    return claims['name'];
  }

  /**
   * logout the user
   */
  logout(): void {
    this.authService.signout();
  }

  /**
   * redirect to home component
   */
  personal(): void {
    this.router.navigate(['personal']);
  }

}
