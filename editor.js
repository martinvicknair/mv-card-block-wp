( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, RichText, useBlockProps } = wp.blockEditor;
	const { PanelBody, TextControl, SelectControl, ToggleControl, BaseControl, ColorPalette, Notice } = wp.components;
	const { Fragment } = wp.element;

	const DEFAULTS = window.MVCardDefaults || {};
	const defaultAccent = DEFAULTS.defaultAccent || "#228B22";
	const settingsUrl = DEFAULTS.settingsUrl || "";

	const ctaTypeOptions = [
		{ label: "Button", value: "button" },
		{ label: "Text link", value: "text" },
		{ label: "Text link with icon 🔗", value: "texticon" },
		{ label: "None", value: "none" },
	];

	registerBlockType( "mv/card", {
		edit: ( props ) => {
			const { attributes, setAttributes } = props;
			const { title, subtitle, content, ctaType, ctaText, ctaUrl, ctaNewTab, accentColor } = attributes;

			const effectiveAccent = accentColor || defaultAccent;

			const blockProps = useBlockProps( {
				className: "mv-card",
				style: effectiveAccent ? { "--mv-card-accent": effectiveAccent } : undefined,
			} );

			const showCtaPreview = ctaType && ctaType !== "none";
			const ctaLabel = ctaText || ( ctaType === "button" ? "View details" : "Call to action" );

			return (
				<Fragment>
					<InspectorControls>
						<PanelBody title="Card Settings" initialOpen={ true }>
							<TextControl
								label="Title"
								value={ title || "" }
								onChange={ ( v ) => setAttributes( { title: v } ) }
							/>
							<TextControl
								label="Subtitle"
								value={ subtitle || "" }
								onChange={ ( v ) => setAttributes( { subtitle: v } ) }
								help="Optional. Slightly smaller and left-aligned."
							/>

							<SelectControl
								label="CTA Type"
								value={ ctaType || "button" }
								options={ ctaTypeOptions }
								onChange={ ( v ) => setAttributes( { ctaType: v } ) }
							/>

							{ ctaType !== "none" && (
								<>
									<TextControl
										label="CTA Text"
										value={ ctaText || "" }
										onChange={ ( v ) => setAttributes( { ctaText: v } ) }
										help="Shown for Button/Text link."
									/>
									<TextControl
										label="CTA URL"
										value={ ctaUrl || "" }
										onChange={ ( v ) => setAttributes( { ctaUrl: v } ) }
										placeholder="https://..."
									/>
									<ToggleControl
										label="Open CTA in new tab"
										checked={ !!ctaNewTab }
										onChange={ ( v ) => setAttributes( { ctaNewTab: !!v } ) }
									/>
								</>
							) }

							<BaseControl
								label="Accent color (optional)"
								help={ accentColor ? "Custom per-card color." : "Using global default. Set a custom color or leave blank." }
							>
								<ColorPalette
									value={ accentColor || defaultAccent }
									onChange={ ( v ) => setAttributes( { accentColor: v || "" } ) }
									enableAlpha={ false }
								/>
								<div style={{ marginTop: "8px" }}>
									<a
										href="#"
										onClick={ ( e ) => { e.preventDefault(); setAttributes( { accentColor: "" } ); } }
										style={{ textDecoration: "none" }}
									>
										Clear per-card accent color
									</a>
								</div>

								{ settingsUrl && (
									<Notice status="info" isDismissible={ false }>
										Global default accent color is configured in <a href={ settingsUrl }>Settings → MV Card Block</a>.
									</Notice>
								) }
							</BaseControl>
						</PanelBody>
					</InspectorControls>

					<div { ...blockProps }>
						<div className="mv-card__caption">
							<RichText
								tagName="div"
								className="mv-card__title"
								value={ title }
								onChange={ ( v ) => setAttributes( { title: v } ) }
								placeholder="Card title…"
								allowedFormats={ [] }
							/>
							<RichText
								tagName="div"
								className="mv-card__subtitle"
								value={ subtitle }
								onChange={ ( v ) => setAttributes( { subtitle: v } ) }
								placeholder="Optional subtitle…"
								allowedFormats={ [] }
							/>
						</div>

						<div className="mv-card__body">
							<RichText
								tagName="div"
								className="mv-card__content"
								value={ content }
								onChange={ ( v ) => setAttributes( { content: v } ) }
								placeholder="Card content…"
							/>
						</div>

						{ showCtaPreview && (
							<div className="mv-card__cta-preview">
								<span className="mv-card__cta-preview-label">CTA preview:</span>

								{ ctaType === "button" && <span className="mv-card__cta-btn">{ ctaLabel }</span> }
								{ ctaType === "text" && <span className="mv-card__cta-link">{ ctaLabel }</span> }
								{ ctaType === "texticon" && (
									<span className="mv-card__cta-link">
										{ ctaLabel } <span className="mv-card__icon" aria-hidden="true">🔗</span>
									</span>
								) }

								{ !ctaUrl && <span className="mv-card__cta-preview-label">(set CTA URL in sidebar)</span> }
							</div>
						) }
					</div>
				</Fragment>
			);
		},

		save: () => null, // Dynamic render in PHP
	} );
} )( window.wp );
