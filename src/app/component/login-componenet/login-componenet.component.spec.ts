import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LoginComponenetComponent } from './login-componenet.component';

describe('LoginComponenetComponent', () => {
  let component: LoginComponenetComponent;
  let fixture: ComponentFixture<LoginComponenetComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ LoginComponenetComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(LoginComponenetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
