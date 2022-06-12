import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EditEmailComponentComponent } from './edit-email-component.component';

describe('EditEmailComponentComponent', () => {
  let component: EditEmailComponentComponent;
  let fixture: ComponentFixture<EditEmailComponentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ EditEmailComponentComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(EditEmailComponentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
