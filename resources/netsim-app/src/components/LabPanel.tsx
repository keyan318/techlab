import { Fragment } from 'react';
import { useStore } from '../store';

// Renders `backtick` spans as inline code and **double-star** spans as bold.
function Rich({ text }: { text: string }) {
  return (
    <>
      {text.split('\n').map((line, li) => (
        <p key={li} className="lab-line">
          {line.split('`').map((part, i) =>
            i % 2 ? (
              <code key={i}>{part}</code>
            ) : (
              <Fragment key={i}>
                {part.split(/\*\*(.+?)\*\*/g).map((seg, si) => (si % 2 ? <strong key={si}>{seg}</strong> : seg))}
              </Fragment>
            )
          )}
        </p>
      ))}
    </>
  );
}

export function LabPanel() {
  const lab = useStore((s) => s.lab);
  const stepIdx = useStore((s) => s.labStep);
  const results = useStore((s) => s.labResults);
  const checking = useStore((s) => s.labChecking);
  const quizAnswers = useStore((s) => s.quizAnswers);
  const setLabStep = useStore((s) => s.setLabStep);
  const answerQuiz = useStore((s) => s.answerQuiz);
  const checkStep = useStore((s) => s.checkStep);
  const exportAttempt = useStore((s) => s.exportAttempt);
  const closeLab = useStore((s) => s.closeLab);

  if (!lab) return null;
  const step = lab.steps[stepIdx];
  const allObjectives = lab.steps.flatMap((s) => s.objectives);
  const passed = allObjectives.filter((o) => results[o.id]?.pass).length;

  return (
    <div className="lab-panel">
      <div className="lab-head">
        <div className="lab-title">{lab.title}</div>
        <button className="tab-close" title="Close lab (keeps the topology)" onClick={() => closeLab()}>
          ×
        </button>
      </div>

      <div className="lab-progress">
        <div className="lab-progress-bar">
          <div
            className="lab-progress-fill"
            style={{ width: `${allObjectives.length ? (passed / allObjectives.length) * 100 : 0}%` }}
          />
        </div>
        <span>
          {passed}/{allObjectives.length} objectives
        </span>
      </div>

      <div className="lab-stepnav">
        <button className="btn" disabled={stepIdx === 0} onClick={() => setLabStep(stepIdx - 1)}>
          ‹
        </button>
        <span>
          Step {stepIdx + 1} of {lab.steps.length}: <strong>{step.title}</strong>
        </span>
        <button
          className="btn"
          disabled={stepIdx >= lab.steps.length - 1}
          onClick={() => setLabStep(stepIdx + 1)}
        >
          ›
        </button>
      </div>

      <div className="lab-scroll">
        <div className="lab-body">
          <Rich text={step.body} />
        </div>

        {step.quiz?.map((q, qi) => {
          const qid = `${lab.id}-${stepIdx}-${qi}`;
          const chosen = quizAnswers[qid];
          return (
            <div key={qid} className="lab-quiz">
              <div className="lab-quiz-q">{q.q}</div>
              {q.options.map((opt, oi) => {
                const isChosen = chosen === oi;
                const cls = isChosen ? (oi === q.answer ? 'correct' : 'wrong') : '';
                return (
                  <label key={oi} className={`lab-quiz-opt ${cls}`}>
                    <input
                      type="radio"
                      name={qid}
                      checked={isChosen}
                      onChange={() => answerQuiz(qid, oi)}
                    />
                    {opt}
                  </label>
                );
              })}
              {chosen !== undefined && (
                <div className={`lab-quiz-verdict ${chosen === q.answer ? 'correct' : 'wrong'}`}>
                  {chosen === q.answer ? 'Correct.' : 'Not quite — try again.'}
                </div>
              )}
            </div>
          );
        })}
      </div>

      {step.objectives.length > 0 && (
        <div className="lab-objectives">
          {step.objectives.map((o) => {
            const r = results[o.id];
            const state = r === undefined ? 'todo' : r.pass ? 'pass' : 'fail';
            return (
              <div key={o.id} className={`lab-objective ${state}`}>
                <span className="lab-obj-mark">{state === 'pass' ? '✓' : state === 'fail' ? '✗' : '○'}</span>
                <span>
                  {o.label}
                  {r && !r.pass && <span className="lab-obj-detail"> — {r.detail}</span>}
                </span>
              </div>
            );
          })}
          <button className="btn lab-check" disabled={checking} onClick={() => checkStep()}>
            {checking ? 'Checking (watch the packets)…' : 'Check objectives'}
          </button>
        </div>
      )}

      <div className="lab-footer">
        <button className="btn" onClick={() => exportAttempt()}>
          Export attempt for submission
        </button>
      </div>
    </div>
  );
}
