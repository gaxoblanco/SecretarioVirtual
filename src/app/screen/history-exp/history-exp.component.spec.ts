import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HistoryExpComponent } from './history-exp.component';

describe('HistoryExpComponent', () => {
  let component: HistoryExpComponent;
  let fixture: ComponentFixture<HistoryExpComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HistoryExpComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(HistoryExpComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
