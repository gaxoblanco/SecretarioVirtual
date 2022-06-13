import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SubcribeScreenComponent } from './subcribe-screen.component';

describe('SubcribeScreenComponent', () => {
  let component: SubcribeScreenComponent;
  let fixture: ComponentFixture<SubcribeScreenComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SubcribeScreenComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SubcribeScreenComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
