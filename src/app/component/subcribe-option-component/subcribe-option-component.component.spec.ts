import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SubcribeOptionComponentComponent } from './subcribe-option-component.component';

describe('SubcribeOptionComponentComponent', () => {
  let component: SubcribeOptionComponentComponent;
  let fixture: ComponentFixture<SubcribeOptionComponentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SubcribeOptionComponentComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SubcribeOptionComponentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
