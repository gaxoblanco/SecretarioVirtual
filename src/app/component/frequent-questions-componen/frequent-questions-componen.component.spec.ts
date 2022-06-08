import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FrequentQuestionsComponenComponent } from './frequent-questions-componen.component';

describe('FrequentQuestionsComponenComponent', () => {
  let component: FrequentQuestionsComponenComponent;
  let fixture: ComponentFixture<FrequentQuestionsComponenComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ FrequentQuestionsComponenComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(FrequentQuestionsComponenComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
