import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SubcribeCardComponentComponent } from './subcribe-card-component.component';

describe('SubcribeCardComponentComponent', () => {
  let component: SubcribeCardComponentComponent;
  let fixture: ComponentFixture<SubcribeCardComponentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SubcribeCardComponentComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SubcribeCardComponentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
