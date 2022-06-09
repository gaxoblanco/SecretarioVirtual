import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UserComponenetComponent } from './user-componenet.component';

describe('UserComponenetComponent', () => {
  let component: UserComponenetComponent;
  let fixture: ComponentFixture<UserComponenetComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ UserComponenetComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(UserComponenetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
