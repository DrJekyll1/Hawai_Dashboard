import { Routes, RouterModule} from '@angular/router';
import {PersonalComponent} from './personal/personal.component';
import {UnauthorizedComponent} from './unauthorized/unauthorized.component';
import {HomeComponent} from './home/home.component';


/**
 * defining routes of the app
 * @type {({path: string; redirectTo: string; pathMatch: string} | {path: string; component: HomeComponent} | {path: string; component: PersonalComponent} | {path: string; component: UnauthorizedComponent} | {path: string; redirectTo: string})[]}
 */
const routes: Routes = [
  {
    path: '',
    redirectTo: 'home',
    pathMatch: 'full'
  },
  {
    path: 'home',
    component: HomeComponent
  },
  {
    path: 'personal',
    component: PersonalComponent
  },
  {
    path: 'Unauthorized',
    component: UnauthorizedComponent
  },
  {
    path: '**',
    redirectTo: 'home'
  }
];

 export let routing = RouterModule.forRoot(routes, {
  initialNavigation: false
 });
