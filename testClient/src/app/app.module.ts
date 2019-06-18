import { BrowserModule } from '@angular/platform-browser';
import { ModuleWithProviders, NgModule } from '@angular/core';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {CdkTableModule} from '@angular/cdk/table';
import { AppComponent } from './app.component';

import { HomeComponent } from './home/home.component';


import { routing } from './app-routing.module';
import {
  MatMenuModule,
  MatIconModule,
  MatToolbarModule,
  MatProgressSpinnerModule,
  MatPaginatorModule,
  MatButtonModule,
  MatGridListModule,
  MatSortModule,
  MatTableModule,

} from '@angular/material';
import { ExternalUrlComponent } from './external-url/external-url.component';
import { HttpClientModule } from '@angular/common/http';
import {Configuration} from 'jasmine-spec-reporter/built/configuration';
import {AuthConfig, JwksValidationHandler, OAuthModule, ValidationHandler} from 'angular-oauth2-oidc';
import { AuthService } from './services/auth.services';
import { LoaderService } from './shared/preload/loader.service';
import { FooterComponent,
         HeaderComponent, } from './shared';
import {RouterModule} from '@angular/router';
import { HttpErrorHandler } from './http-error-handler.services';
import {FileServerServices} from './services/fileServer.services';
import { MessageService } from './message.service';


const rootRouting: ModuleWithProviders = RouterModule.forRoot([], { useHash: true})

@NgModule({
  declarations: [
    AppComponent,

    HomeComponent,

    ExternalUrlComponent,

    FooterComponent,
    HeaderComponent


  ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    CdkTableModule,
    MatMenuModule,
    MatIconModule,
    MatToolbarModule,
    MatButtonModule,
    MatGridListModule,
    MatSortModule,
    MatTableModule,
    MatPaginatorModule,
    routing,
    OAuthModule.forRoot(),
    HttpClientModule,
    MatProgressSpinnerModule,


  ],
  providers: [

    Configuration,
    AuthService,
    LoaderService,
    HttpErrorHandler,
    MessageService,
    FileServerServices
  ],
  bootstrap: [AppComponent]
})
export class AppModule {
  constructor() {


  }
}
