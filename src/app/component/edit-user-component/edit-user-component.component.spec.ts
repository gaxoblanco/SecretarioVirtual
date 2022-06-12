import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EditUserComponentComponent } from './edit-user-component.component';

describe('EditUserComponentComponent', () => {
  let component: EditUserComponentComponent;
  let fixture: ComponentFixture<EditUserComponentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ EditUserComponentComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(EditUserComponentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
