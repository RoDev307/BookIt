import { inject } from '@angular/core';
import { CanActivateFn, CanActivateChildFn } from '@angular/router';
import { SessionService } from '../services/session.service';
export const authGuard: CanActivateFn = () => inject(SessionService).guard('any');
export const adminGuard: CanActivateFn = () => inject(SessionService).guard('admin');
export const adminChildGuard: CanActivateChildFn = () => inject(SessionService).guard('admin');
