import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ResetPwScreenComponent } from './reset-pw-screen.component';

describe('ResetPwScreenComponent', () => {
  let component: ResetPwScreenComponent;
  let fixture: ComponentFixture<ResetPwScreenComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ResetPwScreenComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(ResetPwScreenComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
