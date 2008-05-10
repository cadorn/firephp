<?xml version="1.0"?>

<RDF:RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:em="http://www.mozilla.org/2004/em-rdf#">
  
  <RDF:Description RDF:about="urn:mozilla:extension:FirePHPExtension@firephp.org">
    <em:updates>
      <RDF:Seq>
        <RDF:li RDF:resource="urn:mozilla:extension:FirePHPExtension@firephp.org:%ReleaseVersion%"/>
      </RDF:Seq>
    </em:updates>
  </RDF:Description>
  <RDF:Description RDF:about="urn:mozilla:extension:FirePHPExtension@firephp.org:%ReleaseVersion%">

    <em:version>%ReleaseVersion%</em:version>

    <!-- Firefox -->
    <em:targetApplication>
      <Description>
        <em:id>{ec8030f7-c20a-464f-9b0e-13a3a9e97384}</em:id>
        <em:minVersion>1.5</em:minVersion>
        <em:maxVersion>3.0.0.*</em:maxVersion>
        <em:updateLink>http://www.firephp.org/DownloadRelease/FirePHP-FirefoxExtension-%ReleaseVersion%.xpi</em:updateLink>
      </Description>
    </em:targetApplication>
  </RDF:Description>
</RDF:RDF>
