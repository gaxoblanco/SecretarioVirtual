import { TestBed } from '@angular/core/testing';

import { CreatUserServiceService } from './creat-user-service.service';

describe('CreatUserServiceService', () => {
  let service: CreatUserServiceService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(CreatUserServiceService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
