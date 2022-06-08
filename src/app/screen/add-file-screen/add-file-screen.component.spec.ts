import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddFileScreenComponent } from './add-file-screen.component';

describe('AddFileScreenComponent', () => {
  let component: AddFileScreenComponent;
  let fixture: ComponentFixture<AddFileScreenComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AddFileScreenComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(AddFileScreenComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
