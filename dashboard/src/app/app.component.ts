import {Component, OnInit} from '@angular/core';
import { OAuthService, JwksValidationHandler } from 'angular-oauth2-oidc';
import { Router } from '@angular/router';
import { OAuthConfig } from './auth.config';
import {AuthService} from './services/auth.services';
import { Observable } from 'rxjs/Observable';
import {LoaderService} from './shared/preload/loader.service';
import { User } from './models/user';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {

  signedIn: Observable<boolean>;
  showloader: boolean;
  name: string;

  constructor(private oauthService: OAuthService,
              private router: Router,
              private authService: AuthService,
              private loaderService: LoaderService
  ) {
    this.configureApi();

  }



  private configureApi() {
     this.loaderService.display(true);

     // reload after session is terminated
    this.oauthService.events.filter(e => e.type === 'session_terminated').subscribe(e => {
     window.location.reload();
    });


    this.oauthService.configure(OAuthConfig);
    this.oauthService.setStorage(sessionStorage);

    this.oauthService.tokenValidationHandler = new JwksValidationHandler();

    // Loads discovery document & tries login.
    this.oauthService.loadDiscoveryDocument().then((doc: any) => {
      // Stores discovery document.
      this.authService.setItem('discoveryDocument', doc.info.discoveryDocument);
      // Tries login.


      this.oauthService.tryLogin().then(() => {
        // Manages consent error.
        if (window.location.search && window.location.search.match(/\^?error=consent_required/) != null) {
          this.router.navigate(['/forbidden']);
        }

      });

      if (this.oauthService.hasValidAccessToken()) {

        console.log('AccessToken: ', this.oauthService.getAccessToken());

        this.oauthService.loadUserProfile().then(() => {
          this.authService.init();

          // Gets the redirect URL.
          // If no redirect has been set, uses the default.
          const redirect: string = this.authService.getItem('redirectUrl')
            ? this.authService.getItem('redirectUrl')
            : '/home';
          // Redirects the user.
          this.router.navigate([redirect]);
          this.loaderService.display(false);
        });
      } else {
        this.oauthService.initImplicitFlow();
      }

    });


    // Optional
    this.oauthService.setupAutomaticSilentRefresh();

    this.oauthService.events.subscribe(e => {
      console.log('oauth/oidc event', e);
    });

    this.oauthService.events.filter(e => e.type === 'token_received').subscribe(e => {
       this.oauthService.loadUserProfile();
    });

    // Already authorized.
    if (this.oauthService.hasValidAccessToken()) {
      this.authService.init();
    }
  }


  ngOnInit() {
    this.signedIn = this.authService.isSignedIn();
    this.loaderService.status.subscribe((val: boolean) => {
      this.showloader = val;
    });
    this.authService.userChanged().subscribe(
      (user: User) => {
        this.name = user.givenName;
      });

  }

  login(): void {
    this.oauthService.initImplicitFlow();
  }


}
