import { Routes, RouterModule} from '@angular/router';
import { CustomPreloadingStrategy } from './shared/preload/custom-preloading.strategy';
import {HomeComponent} from './home/home.component';

import { ModuleWithProviders, NgModule } from '@angular/core';

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
    path: '**',
    redirectTo: 'home'
  }
];


// export let rootRouting: ModuleWithProviders = RouterModule.forRoot([], { useHash: true});

 export let routing = RouterModule.forRoot(routes, {
   // preloadingStrategy: CustomPreloadingStrategy,
  // useHash: true,
  initialNavigation: false
 });
