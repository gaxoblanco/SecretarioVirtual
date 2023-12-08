import { ComponentFixture, TestBed } from '@angular/core/testing';

import { JusticeComponent } from './justice.component';

describe('JusticeComponent', () => {
  let component: JusticeComponent;
  let fixture: ComponentFixture<JusticeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [JusticeComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(JusticeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
