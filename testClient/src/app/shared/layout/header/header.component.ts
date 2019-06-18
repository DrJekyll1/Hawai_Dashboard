import { Component, OnInit } from '@angular/core';
import { OAuthService } from 'angular-oauth2-oidc';
import { Observable } from 'rxjs/Observable';
import {AuthService} from '../../../services/auth.services';

@Component({
  selector: 'layout-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

  signedIn: Observable<boolean>;

  constructor(private oauthService: OAuthService,
  private authService: AuthService) {

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

  logout(): void {
    this.authService.signout();
  }

}
